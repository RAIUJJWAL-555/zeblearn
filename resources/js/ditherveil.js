// WebGL2 DitherVeil Engine (Native high-performance implementation of resources/reactItems/DitherVeil.jsx)

const DEFAULT_SRC = 'https://images.unsplash.com/photo-1737071371043-761e02b1ef95?q=80&w=1400&auto=format&fit=crop';

const ORDERED = { bayer: 0, noise: 1, lines: 2 };

const KERNELS = {
  atkinson: [
    [1, 0, 1 / 8],
    [2, 0, 1 / 8],
    [-1, 1, 1 / 8],
    [0, 1, 1 / 8],
    [1, 1, 1 / 8],
    [0, 2, 1 / 8]
  ],
  floyd: [
    [1, 0, 7 / 16],
    [-1, 1, 3 / 16],
    [0, 1, 5 / 16],
    [1, 1, 1 / 16]
  ]
};

const MASK_SCALE = 0.5;
const MAX_BURSTS = 4;
const BURST_SECONDS = 1.2;
const HOLD = 1.6;
const INTRO_MS = 1100;

const hexToRgb = hex => {
  let h = String(hex || '').replace('#', '');
  if (h.length === 3) h = h.replace(/./g, c => c + c);
  const n = parseInt(h.slice(0, 6), 16);
  return Number.isNaN(n) ? [0, 0, 0] : [((n >> 16) & 255) / 255, ((n >> 8) & 255) / 255, (n & 255) / 255];
};

const fitScale = (w, h, iw, ih, contain) => {
  const ratio = w / h / (iw / ih);
  if (contain) return ratio > 1 ? [ratio, 1] : [1, 1 / ratio];
  return ratio > 1 ? [1, 1 / ratio] : [ratio, 1];
};

const wanderAt = (t, w, h) => [
  w * (0.5 + 0.33 * Math.sin(t * 0.53) + 0.08 * Math.sin(t * 1.31 + 0.6)),
  h * (0.5 + 0.28 * Math.sin(t * 0.71 + 1.1) + 0.07 * Math.cos(t * 1.57))
];

const measureEdge = (context, image) => {
  const size = 32;
  context.canvas.width = size;
  context.canvas.height = size;
  context.drawImage(image, 0, 0, size, size);
  const data = context.getImageData(0, 0, size, size).data;
  const sum = [0, 0, 0];
  const squares = [0, 0, 0];
  let count = 0;
  for (let y = 0; y < size; y++) {
    for (let x = 0; x < size; x++) {
      if (x > 0 && y > 0 && x < size - 1 && y < size - 1) continue;
      for (let c = 0; c < 3; c++) {
        const v = data[(y * size + x) * 4 + c] / 255;
        sum[c] += v;
        squares[c] += v * v;
      }
      count++;
    }
  }
  const matte = [sum[0] / count, sum[1] / count, sum[2] / count];
  const spread =
    matte.reduce((total, mean, c) => total + Math.sqrt(Math.max(0, squares[c] / count - mean * mean)), 0) / 3;
  return { matte, plain: spread < 0.06 };
};

let blueNoise = null;

const getBlueNoise = () => {
  if (blueNoise) return blueNoise;
  const size = 64;
  const n = size * size;
  const wrap = size - 1;
  const taps = [];
  for (let dy = -6; dy <= 6; dy++) {
    for (let dx = -6; dx <= 6; dx++) taps.push(dx, dy, Math.exp(-(dx * dx + dy * dy) / 4.5));
  }
  const energy = new Float32Array(n);
  const on = new Uint8Array(n);
  const splat = (i, sign) => {
    const x = i % size;
    const y = (i - x) / size;
    for (let k = 0; k < taps.length; k += 3) {
      energy[((y + taps[k + 1]) & wrap) * size + ((x + taps[k]) & wrap)] += sign * taps[k + 2];
    }
  };
  const find = (state, highest) => {
    let best = 0;
    let value = highest ? -Infinity : Infinity;
    for (let i = 0; i < n; i++) {
      if (on[i] !== state) continue;
      if (highest ? energy[i] > value : energy[i] < value) {
        value = energy[i];
        best = i;
      }
    }
    return best;
  };
  let seed = 7;
  const random = () => {
    seed = (seed * 16807) % 2147483647;
    return seed / 2147483647;
  };
  const initial = Math.round(n * 0.1);
  for (let placed = 0; placed < initial; ) {
    const i = Math.floor(random() * n);
    if (on[i]) continue;
    on[i] = 1;
    splat(i, 1);
    placed++;
  }
  for (let guard = 0; guard < n; guard++) {
    const cluster = find(1, true);
    on[cluster] = 0;
    splat(cluster, -1);
    const gap = find(0, false);
    on[gap] = 1;
    splat(gap, 1);
    if (gap === cluster) break;
  }
  const seedOn = on.slice();
  const seedEnergy = energy.slice();
  const rank = new Uint16Array(n);
  for (let r = initial - 1; r >= 0; r--) {
    const cluster = find(1, true);
    on[cluster] = 0;
    splat(cluster, -1);
    rank[cluster] = r;
  }
  on.set(seedOn);
  energy.set(seedEnergy);
  for (let r = initial; r < n; r++) {
    const gap = find(0, false);
    on[gap] = 1;
    splat(gap, 1);
    rank[gap] = r;
  }
  const data = new Uint8Array(n * 4);
  for (let i = 0; i < n; i++) {
    const v = Math.floor((rank[i] / n) * 256);
    data[i * 4] = v;
    data[i * 4 + 1] = v;
    data[i * 4 + 2] = v;
    data[i * 4 + 3] = 255;
  }
  blueNoise = data;
  return data;
};

const diffuse = (pixels, cols, rows, kernel, levels, rgb, grade) => {
  const channels = rgb ? 3 : 1;
  const values = new Float32Array(cols * rows * channels);
  for (let i = 0; i < cols * rows; i++) {
    const r = pixels[i * 4] / 255;
    const g = pixels[i * 4 + 1] / 255;
    const b = pixels[i * 4 + 2] / 255;
    if (rgb) {
      values[i * 3] = grade(r);
      values[i * 3 + 1] = grade(g);
      values[i * 3 + 2] = grade(b);
    } else {
      values[i] = grade(0.2126 * r + 0.7152 * g + 0.0722 * b);
    }
  }
  const steps = levels - 1;
  const out = new Uint8Array(cols * rows * 4);
  for (let y = 0; y < rows; y++) {
    const dir = y & 1 ? -1 : 1;
    for (let i = 0; i < cols; i++) {
      const x = dir > 0 ? i : cols - 1 - i;
      const p = y * cols + x;
      for (let c = 0; c < channels; c++) {
        const old = values[p * channels + c];
        const q = Math.min(steps, Math.max(0, Math.round(old * steps))) / steps;
        const err = old - q;
        const byte = Math.round(q * 255);
        if (rgb) out[p * 4 + c] = byte;
        else out[p * 4] = out[p * 4 + 1] = out[p * 4 + 2] = byte;
        for (let k = 0; k < kernel.length; k++) {
          const nx = x + kernel[k][0] * dir;
          const ny = y + kernel[k][1];
          if (nx < 0 || nx >= cols || ny >= rows) continue;
          values[(ny * cols + nx) * channels + c] += err * kernel[k][2];
        }
      }
      out[p * 4 + 3] = 255;
    }
  }
  return out;
};

const vertex = `#version 300 es
in vec2 position;
in vec2 uv;
out vec2 vUv;
void main() {
  vUv = uv;
  gl_Position = vec4(position, 0.0, 1.0);
}
`;

const maskFragment = `#version 300 es
precision highp float;

uniform sampler2D tPrev;
uniform vec2 uSize;
uniform vec2 uFrom;
uniform vec2 uTo;
uniform float uRadius;
uniform float uSoftness;
uniform float uStrength;
uniform float uFade;
uniform float uHold;

in vec2 vUv;
out vec4 fragColor;

float strokeDistance(vec2 p, vec2 a, vec2 b) {
  vec2 ab = b - a;
  float h = clamp(dot(p - a, ab) / max(dot(ab, ab), 0.0001), 0.0, 1.0);
  return length(p - a - ab * h);
}

void main() {
  vec2 p = vec2(vUv.x, 1.0 - vUv.y) * uSize;
  float trail = max(texture(tPrev, vUv).r - uFade, 0.0);
  float band = max(uRadius * uSoftness, 1.0) * uHold;
  float d = strokeDistance(p, uFrom, uTo);
  trail = max(trail, clamp((uRadius - d) / band, 0.0, 1.0) * uStrength);
  fragColor = vec4(trail, 0.0, 0.0, 1.0);
}
`;

const viewFragment = `#version 300 es
precision highp float;
precision highp int;

uniform sampler2D tImage;
uniform sampler2D tMask;
uniform sampler2D tNoise;
uniform sampler2D tDiffused;
uniform vec2 uResolution;
uniform vec2 uCover;
uniform float uLod;
uniform float uCell;
uniform int uPattern;
uniform int uPalette;
uniform float uLevels;
uniform vec3 uInk;
uniform vec3 uPaper;
uniform vec3 uRimColor;
uniform float uRim;
uniform float uContrast;
uniform float uBrightness;
uniform float uReverse;
uniform float uIntro;
uniform float uHold;
uniform vec3 uMatte;
uniform float uKey;
uniform vec2 uSize;
uniform vec4 uBursts[4];
uniform float uBurstWidth;

in vec2 vUv;
out vec4 fragColor;

float bayer(vec2 cell) {
  ivec2 p = ivec2(mod(cell, 8.0));
  int v = p.x ^ p.y;
  int m = ((v & 1) << 5) | ((p.y & 1) << 4) | ((v & 2) << 2) | ((p.y & 2) << 1) | ((v & 4) >> 1) | ((p.y & 4) >> 2);
  return (float(m) + 0.5) / 64.0;
}

float blueNoise(vec2 cell) {
  return (texelFetch(tNoise, ivec2(mod(cell, 64.0)), 0).r * 255.0 + 0.5) / 256.0;
}

float engraving(vec2 cell) {
  float period = 6.0;
  float f = (mod(cell.x + cell.y, period) + 0.5) / period;
  return clamp(abs(f * 2.0 - 1.0) + (bayer(cell) - 0.5) * (2.0 / period), 0.0, 1.0);
}

vec2 imageUv(vec2 uv) {
  vec2 flipped = vec2(uv.x, 1.0 - uv.y);
  return (flipped - 0.5) * uCover + 0.5;
}

float within(vec2 p) {
  vec2 s = step(vec2(0.0), p) * step(p, vec2(1.0));
  return s.x * s.y;
}

vec3 grade(vec3 c) {
  return pow(clamp((c - 0.5) * uContrast + 0.5 + uBrightness, 0.0, 1.0), vec3(1.6));
}

float shockwave(vec2 p) {
  float value = 0.0;
  for (int i = 0; i < 4; i++) {
    vec4 burst = uBursts[i];
    if (burst.w <= 0.0) continue;
    float offset = distance(p, burst.xy) - burst.z;
    float edge = offset > 0.0 ? offset / (uBurstWidth * 0.35) : -offset / uBurstWidth;
    value = max(value, clamp(1.0 - edge, 0.0, 1.0) * burst.w);
  }
  return value;
}

vec3 toned(vec3 c) {
  return uPalette == 1 ? grade(c) : grade(vec3(dot(c, vec3(0.2126, 0.7152, 0.0722))));
}

vec3 quantize(vec3 v, float t) {
  float steps = max(uLevels - 1.0, 1.0);
  vec3 s = v * steps;
  vec3 base = floor(s);
  return min(base + step(vec3(t), s - base), vec3(steps)) / steps;
}

void main() {
  vec2 px = vec2(gl_FragCoord.x, uResolution.y - gl_FragCoord.y);
  vec2 cell = floor(px / uCell);
  vec2 center = (cell + 0.5) * uCell;
  vec2 cellUv = vec2(center.x / uResolution.x, 1.0 - center.y / uResolution.y);

  vec2 sampleUv = imageUv(cellUv);
  float framed = within(sampleUv);
  vec3 level;
  if (uPattern == 3) {
    int totalRows = int(ceil(uResolution.y / uCell));
    ivec2 fetchCoord = ivec2(int(cell.x), max(0, totalRows - 1 - int(cell.y)));
    level = texelFetch(tDiffused, fetchCoord, 0).rgb;
  } else {
    vec3 c = mix(uMatte, textureLod(tImage, sampleUv, uLod).rgb, framed);
    float t = uPattern == 1 ? blueNoise(cell) : (uPattern == 2 ? engraving(cell) : bayer(cell));
    level = quantize(toned(c), t);
  }

  vec3 color = mix(uInk, mix(uInk, uPaper, level), max(framed, uKey));
  vec3 backdrop = mix(uInk, uPaper, toned(uMatte));
  vec2 photoUv = imageUv(vUv);
  vec3 raw = texture(tImage, photoUv).rgb;
  float plain = uKey * (1.0 - smoothstep(0.05, 0.22, distance(raw, uMatte)));
  vec3 photo = mix(mix(uInk, backdrop, uKey), mix(raw, backdrop, plain), within(photoUv));

  vec2 point = vec2(cellUv.x, 1.0 - cellUv.y) * uSize;
  float mask = max(clamp(texture(tMask, cellUv).r * uHold, 0.0, 1.0), shockwave(point));
  float shown = mix(mask, 1.0 - mask, uReverse);
  float order = bayer(cell.yx);
  float low = order * (1.0 - uRim);
  color = mix(color, uRimColor, step(low, shown) * step(0.001, uRim));
  color = mix(color, photo, step(low + uRim, shown));

  vec2 aspect = vec2(uResolution.x / uResolution.y, 1.0);
  float spread = length((cellUv - 0.5) * aspect) / length(aspect * 0.5);
  float appear = step(spread * 0.72 + bayer(cell + vec2(3.0, 5.0)) * 0.28, uIntro * 1.001);
  vec3 finalColor = mix(uInk, color, appear);
  float isForeground = max(step(0.02, distance(color, uInk)), step(0.01, shown));
  float a = isForeground * appear;
  fragColor = vec4(finalColor * a, a);
}
`;

// Minimal WebGL2 Abstraction matching OGL's exact interface
class MiniProgram {
  constructor(gl, { vertex: vsSource, fragment: fsSource, uniforms = {} }) {
    this.gl = gl;
    this.uniforms = uniforms;
    const vs = gl.createShader(gl.VERTEX_SHADER);
    gl.shaderSource(vs, vsSource);
    gl.compileShader(vs);

    const fs = gl.createShader(gl.FRAGMENT_SHADER);
    gl.shaderSource(fs, fsSource);
    gl.compileShader(fs);

    const program = gl.createProgram();
    gl.attachShader(program, vs);
    gl.attachShader(program, fs);
    gl.linkProgram(program);

    this.program = program;
    this.locations = {};
    for (const name of Object.keys(uniforms)) {
      this.locations[name] = gl.getUniformLocation(program, name);
    }
  }

  use() {
    this.gl.useProgram(this.program);
  }
}

class MiniTexture {
  constructor(gl, { image = null, width = 1, height = 1, minFilter = gl.LINEAR, magFilter = gl.LINEAR, generateMipmaps = false } = {}) {
    this.gl = gl;
    this.texture = gl.createTexture();
    this.width = width;
    this.height = height;
    this.minFilter = minFilter;
    this.magFilter = magFilter;
    this.generateMipmaps = generateMipmaps;
    this.image = image;
    this.needsUpdate = false;
    this.update();
  }

  update() {
    const gl = this.gl;
    gl.bindTexture(gl.TEXTURE_2D, this.texture);
    gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_MIN_FILTER, this.minFilter);
    gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_MAG_FILTER, this.magFilter);
    gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_WRAP_S, gl.CLAMP_TO_EDGE);
    gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_WRAP_T, gl.CLAMP_TO_EDGE);

    if (this.image) {
      if (this.image instanceof Uint8Array || this.image instanceof Float32Array) {
        gl.texImage2D(gl.TEXTURE_2D, 0, gl.RGBA, this.width, this.height, 0, gl.RGBA, gl.UNSIGNED_BYTE, this.image);
      } else {
        gl.texImage2D(gl.TEXTURE_2D, 0, gl.RGBA, gl.RGBA, gl.UNSIGNED_BYTE, this.image);
      }
      if (this.generateMipmaps) {
        gl.generateMipmap(gl.TEXTURE_2D);
      }
    }
  }
}

class MiniRenderTarget {
  constructor(gl, { width = 2, height = 2, internalFormat = gl.RGBA, minFilter = gl.LINEAR, magFilter = gl.LINEAR }) {
    this.gl = gl;
    this.width = width;
    this.height = height;
    this.buffer = gl.createFramebuffer();
    this.texture = new MiniTexture(gl, {
      width,
      height,
      minFilter,
      magFilter,
      generateMipmaps: false
    });

    gl.bindFramebuffer(gl.FRAMEBUFFER, this.buffer);
    gl.texImage2D(gl.TEXTURE_2D, 0, internalFormat, width, height, 0, gl.RGBA, gl.UNSIGNED_BYTE, null);
    gl.framebufferTexture2D(gl.FRAMEBUFFER, gl.COLOR_ATTACHMENT0, gl.TEXTURE_2D, this.texture.texture, 0);
    gl.bindFramebuffer(gl.FRAMEBUFFER, null);
  }
}

export function initDitherVeil(container, customOptions = {}) {
  if (!container) return null;

  const s = {
    src: customOptions.src ?? DEFAULT_SRC,
    fit: customOptions.fit ?? 'contain',
    pattern: customOptions.pattern ?? 'floyd',
    pixelSize: customOptions.pixelSize ?? 2,
    levels: customOptions.levels ?? 2,
    palette: customOptions.palette ?? 'duotone',
    inkColor: customOptions.inkColor ?? '#FAFAF8',
    paperColor: customOptions.paperColor ?? '#120f17',
    contrast: customOptions.contrast ?? 1.15,
    brightness: customOptions.brightness ?? 0,
    revealRadius: customOptions.revealRadius ?? 200,
    softness: customOptions.softness ?? 0.6,
    linger: customOptions.linger ?? 1,
    rimColor: customOptions.rimColor ?? '#FB6221',
    rim: customOptions.rim ?? 0,
    reverse: customOptions.reverse ?? false,
    wander: customOptions.wander ?? false,
    clickBurst: customOptions.clickBurst ?? true
  };

  const canvas = document.createElement('canvas');
  canvas.style.display = 'block';
  canvas.style.width = '100%';
  canvas.style.height = '100%';
  canvas.setAttribute('aria-hidden', 'true');
  container.appendChild(canvas);

  const gl = canvas.getContext('webgl2', { alpha: true, premultipliedAlpha: true, antialias: false, depth: false, stencil: false });
  if (!gl) {
    console.warn('WebGL2 not available for DitherVeil');
    return null;
  }

  const dpr = Math.min(window.devicePixelRatio || 1, 2);
  const vao = gl.createVertexArray();
  gl.bindVertexArray(vao);

  const posBuf = gl.createBuffer();
  gl.bindBuffer(gl.ARRAY_BUFFER, posBuf);
  gl.bufferData(gl.ARRAY_BUFFER, new Float32Array([-1, -1, 3, -1, -1, 3]), gl.STATIC_DRAW);
  gl.enableVertexAttribArray(0);
  gl.vertexAttribPointer(0, 2, gl.FLOAT, false, 0, 0);

  const uvBuf = gl.createBuffer();
  gl.bindBuffer(gl.ARRAY_BUFFER, uvBuf);
  gl.bufferData(gl.ARRAY_BUFFER, new Float32Array([0, 0, 2, 0, 0, 2]), gl.STATIC_DRAW);
  gl.enableVertexAttribArray(1);
  gl.vertexAttribPointer(1, 2, gl.FLOAT, false, 0, 0);

  gl.bindVertexArray(null);

  const imageTexture = new MiniTexture(gl, {
    minFilter: gl.LINEAR_MIPMAP_LINEAR,
    magFilter: gl.LINEAR,
    generateMipmaps: true
  });
  const noiseTexture = new MiniTexture(gl, {
    image: new Uint8Array(4),
    width: 1,
    height: 1,
    minFilter: gl.NEAREST,
    magFilter: gl.NEAREST
  });
  const diffusedTexture = new MiniTexture(gl, {
    image: new Uint8Array(4),
    width: 1,
    height: 1,
    minFilter: gl.NEAREST,
    magFilter: gl.NEAREST
  });

  const createMask = (w, h) => new MiniRenderTarget(gl, { width: w, height: h });
  const destroyMask = target => {
    gl.deleteFramebuffer(target.buffer);
    gl.deleteTexture(target.texture.texture);
  };

  let masks = [createMask(2, 2), createMask(2, 2)];

  const maskUniforms = {
    tPrev: { value: masks[0].texture },
    uSize: { value: [1, 1] },
    uFrom: { value: [0, 0] },
    uTo: { value: [0, 0] },
    uRadius: { value: 1 },
    uSoftness: { value: 0.5 },
    uStrength: { value: 0 },
    uFade: { value: 1 },
    uHold: { value: HOLD }
  };

  const viewUniforms = {
    tImage: { value: imageTexture },
    tMask: { value: masks[0].texture },
    tNoise: { value: noiseTexture },
    tDiffused: { value: diffusedTexture },
    uResolution: { value: [1, 1] },
    uCover: { value: [1, 1] },
    uLod: { value: 0 },
    uCell: { value: 3 },
    uPattern: { value: 0 },
    uPalette: { value: 0 },
    uLevels: { value: 2 },
    uInk: { value: [0, 0, 0] },
    uPaper: { value: [1, 1, 1] },
    uRimColor: { value: [1, 1, 1] },
    uRim: { value: 0 },
    uContrast: { value: 1 },
    uBrightness: { value: 0 },
    uReverse: { value: 0 },
    uIntro: { value: 0 },
    uHold: { value: HOLD },
    uMatte: { value: [0, 0, 0] },
    uKey: { value: 0 },
    uSize: { value: [1, 1] },
    uBursts: { value: new Float32Array(MAX_BURSTS * 4) },
    uBurstWidth: { value: 60 }
  };

  const maskProgram = new MiniProgram(gl, { vertex, fragment: maskFragment, uniforms: maskUniforms });
  const viewProgram = new MiniProgram(gl, { vertex, fragment: viewFragment, uniforms: viewUniforms });

  const setUniforms = (prog, uniforms) => {
    let textureUnit = 0;
    for (const [name, u] of Object.entries(uniforms)) {
      const loc = prog.locations[name];
      if (!loc) continue;
      const val = u.value;
      if (val instanceof MiniTexture) {
        gl.activeTexture(gl.TEXTURE0 + textureUnit);
        gl.bindTexture(gl.TEXTURE_2D, val.texture);
        gl.uniform1i(loc, textureUnit);
        textureUnit++;
      } else if (typeof val === 'number') {
        gl.uniform1f(loc, val);
      } else if (Array.isArray(val) || val instanceof Float32Array) {
        if (val.length === 2) gl.uniform2f(loc, val[0], val[1]);
        else if (val.length === 3) gl.uniform3f(loc, val[0], val[1], val[2]);
        else if (val.length === 4) gl.uniform4f(loc, val[0], val[1], val[2], val[3]);
        else if (val.length === 16) gl.uniform4fv(loc, val);
      }
    }
  };

  const renderPass = (prog, uniforms, target = null) => {
    if (target) {
      gl.bindFramebuffer(gl.FRAMEBUFFER, target.buffer);
      gl.viewport(0, 0, target.width, target.height);
    } else {
      gl.bindFramebuffer(gl.FRAMEBUFFER, null);
      gl.viewport(0, 0, canvas.width, canvas.height);
    }
    prog.use();
    setUniforms(prog, uniforms);
    gl.bindVertexArray(vao);
    gl.drawArrays(gl.TRIANGLES, 0, 3);
    gl.bindVertexArray(null);
  };

  const sampler = document.createElement('canvas');
  const samplerContext = sampler.getContext('2d', { willReadFrequently: true });

  let image = null;
  let introStart = 0;
  let diffusedKey = '';
  let diffusionBlocked = false;
  let width = 1;
  let height = 1;
  let visible = true;
  let raf = 0;
  let last = performance.now();
  let trailUntil = 0;
  let presence = 0;
  let drift = 0;
  const pointer = { x: 0, y: 0, inside: false, fresh: true, placed: false };
  const brush = { x: 0, y: 0, px: 0, py: 0 };
  const bursts = [];
  let alive = true;

  const reducedMotion = window.matchMedia?.('(prefers-reduced-motion: reduce)').matches;

  const applyLayout = () => {
    width = Math.max(1, container.clientWidth);
    height = Math.max(1, container.clientHeight);
    canvas.width = Math.round(width * dpr);
    canvas.height = Math.round(height * dpr);
    viewUniforms.uResolution.value = [canvas.width, canvas.height];
    maskUniforms.uSize.value = [width, height];
    viewUniforms.uSize.value = [width, height];

    const mw = Math.max(2, Math.round(width * MASK_SCALE));
    const mh = Math.max(2, Math.round(height * MASK_SCALE));
    if (mw !== masks[0].width || mh !== masks[0].height) {
      const prev = masks;
      masks = [createMask(mw, mh), createMask(mw, mh)];
      maskUniforms.tPrev.value = prev[0].texture;
      maskUniforms.uStrength.value = 0;
      maskUniforms.uFade.value = 0;
      renderPass(maskProgram, maskUniforms, masks[0]);
      prev.forEach(destroyMask);
    }

    if (!pointer.placed) {
      pointer.x = width / 2;
      pointer.y = height / 2;
    }
  };

  const updateDiffusion = (cell) => {
    if (!image || !samplerContext) return;
    const cols = Math.ceil(canvas.width / cell);
    const rows = Math.ceil(canvas.height / cell);
    const key = [s.pattern, s.levels, s.palette, s.contrast, s.brightness, s.fit, cols, rows, canvas.width, canvas.height].join('|');
    if (key === diffusedKey) return;
    diffusedKey = key;

    const [cx, cy] = viewUniforms.uCover.value;
    const iw = image.naturalWidth;
    const ih = image.naturalHeight;
    sampler.width = cols;
    sampler.height = rows;
    samplerContext.imageSmoothingEnabled = true;
    samplerContext.imageSmoothingQuality = 'high';

    const [mr, mg, mb] = viewUniforms.uMatte.value;
    samplerContext.fillStyle = `rgb(${mr * 255}, ${mg * 255}, ${mb * 255})`;
    samplerContext.fillRect(0, 0, cols, rows);

    const sx = (0.5 - 0.5 * cx) * iw;
    const sy = (0.5 - 0.5 * cy) * ih;
    const sw = ((cols * cell) / canvas.width) * cx * iw;
    const sh = ((rows * cell) / canvas.height) * cy * ih;
    const x0 = Math.max(sx, 0);
    const y0 = Math.max(sy, 0);
    const x1 = Math.min(sx + sw, iw);
    const y1 = Math.min(sy + sh, ih);
    if (x1 > x0 && y1 > y0) {
      samplerContext.drawImage(image, x0, y0, x1 - x0, y1 - y0, ((x0 - sx) / sw) * cols, ((y0 - sy) / sh) * rows, ((x1 - x0) / sw) * cols, ((y1 - y0) / sh) * rows);
    }

    const grade = v => Math.pow(Math.min(1, Math.max(0, (v - 0.5) * s.contrast + 0.5 + s.brightness)), 1.6);
    const pixels = samplerContext.getImageData(0, 0, cols, rows).data;
    diffusedTexture.image = diffuse(pixels, cols, rows, KERNELS[s.pattern] || KERNELS.floyd, s.levels, s.palette === 'rgb', grade);
    diffusedTexture.width = cols;
    diffusedTexture.height = rows;
    diffusedTexture.update();
  };

  const frame = now => {
    if (!alive) return;
    raf = 0;
    const dt = Math.min(0.05, Math.max(0, (now - last) / 1000));
    last = now;

    const wanderOn = s.wander && !reducedMotion;
    let targetX = pointer.x;
    let targetY = pointer.y;
    if (!pointer.inside && wanderOn) {
      drift = Math.min(1, drift + dt / 1.2);
      const [wx, wy] = wanderAt(now / 1000, width, height);
      const k = drift * drift * (3 - 2 * drift);
      targetX += (wx - targetX) * k;
      targetY += (wy - targetY) * k;
    } else {
      drift = 0;
    }

    presence += ((pointer.inside || wanderOn ? 1 : 0) - presence) * (1 - Math.exp(-dt / 0.16));

    if (pointer.fresh) {
      brush.x = brush.px = targetX;
      brush.y = brush.py = targetY;
      pointer.fresh = false;
    } else {
      const follow = 1 - Math.exp(-dt / 0.035);
      brush.x += (targetX - brush.x) * follow;
      brush.y += (targetY - brush.y) * follow;
    }

    const burstData = viewUniforms.uBursts.value;
    burstData.fill(0);
    for (let i = bursts.length - 1; i >= 0; i--) {
      if ((now - bursts[i].start) / 1000 >= BURST_SECONDS) bursts.splice(i, 1);
    }
    bursts.forEach((b, i) => {
      const k = Math.max(0, (now - b.start) / 1000 / BURST_SECONDS);
      const reach = Math.hypot(Math.max(b.x, width - b.x), Math.max(b.y, height - b.y)) + viewUniforms.uBurstWidth.value;
      burstData[i * 4] = b.x;
      burstData[i * 4 + 1] = b.y;
      burstData[i * 4 + 2] = reach * Math.sin((k * Math.PI) / 2);
      burstData[i * 4 + 3] = 1 - k * k * k;
    });

    if (presence > 0.002) trailUntil = now + s.linger * 1000 + 150;

    const fade = s.linger > 0 ? dt / s.linger : 1;
    maskUniforms.tPrev.value = masks[0].texture;
    maskUniforms.uFrom.value = [brush.px, brush.py];
    maskUniforms.uTo.value = [brush.x, brush.y];
    maskUniforms.uRadius.value = s.revealRadius * (0.45 + 0.55 * presence);
    maskUniforms.uSoftness.value = s.softness;
    maskUniforms.uStrength.value = presence;
    maskUniforms.uFade.value = Math.max(fade, 1.5 / 255);
    viewUniforms.uBurstWidth.value = Math.max(60, s.revealRadius * 0.9);

    renderPass(maskProgram, maskUniforms, masks[1]);
    masks.reverse();
    brush.px = brush.x;
    brush.py = brush.y;

    const cell = Math.max(1, Math.round(s.pixelSize * dpr));
    if (image) {
      viewUniforms.uCover.value = fitScale(canvas.width, canvas.height, image.naturalWidth, image.naturalHeight, s.fit === 'contain');
    }

    let patternIndex = ORDERED[s.pattern] ?? 0;
    if (KERNELS[s.pattern]) {
      patternIndex = 0;
      if (image && !diffusionBlocked) {
        try {
          updateDiffusion(cell);
          patternIndex = 3;
        } catch {
          diffusionBlocked = true;
        }
      }
    }

    if (patternIndex === 1 && noiseTexture.width !== 64) {
      noiseTexture.image = getBlueNoise();
      noiseTexture.width = 64;
      noiseTexture.height = 64;
      noiseTexture.update();
    }

    const texelsPerPixel = image ? (viewUniforms.uCover.value[0] * image.naturalWidth) / canvas.width : 1;
    viewUniforms.tMask.value = masks[0].texture;
    viewUniforms.uCell.value = cell;
    viewUniforms.uLod.value = Math.log2(Math.max(cell * texelsPerPixel, 1));
    viewUniforms.uPattern.value = patternIndex;
    viewUniforms.uPalette.value = s.palette === 'rgb' ? 1 : 0;
    viewUniforms.uLevels.value = Math.max(2, Math.round(s.levels));
    viewUniforms.uInk.value = hexToRgb(s.inkColor);
    viewUniforms.uPaper.value = hexToRgb(s.paperColor);
    viewUniforms.uRimColor.value = hexToRgb(s.rimColor);
    viewUniforms.uRim.value = Math.min(Math.max(s.rim, 0), 0.95);
    viewUniforms.uContrast.value = s.contrast;
    viewUniforms.uBrightness.value = s.brightness;
    viewUniforms.uReverse.value = s.reverse ? 1 : 0;
    const intro = image ? Math.min(1, (now - introStart) / INTRO_MS) : 0;
    viewUniforms.uIntro.value = 1 - Math.pow(1 - intro, 2);

    renderPass(viewProgram, viewUniforms, null);

    const busy = pointer.inside || wanderOn || presence > 0.002 || bursts.length > 0 || now < trailUntil || (image && intro < 1);
    if (busy && visible && alive) raf = requestAnimationFrame(frame);
  };

  const wake = () => {
    if (raf || !visible || !alive) return;
    last = performance.now();
    raf = requestAnimationFrame(frame);
  };

  const img = new Image();
  img.crossOrigin = 'anonymous';
  img.decoding = 'async';
  img.onload = () => {
    if (!alive) return;
    image = img;
    imageTexture.image = img;
    imageTexture.update();
    try {
      if (samplerContext) {
        const edge = measureEdge(samplerContext, img);
        viewUniforms.uMatte.value = edge.matte;
        viewUniforms.uKey.value = edge.plain ? 1 : 0;
      }
    } catch {}
    introStart = performance.now();
    wake();
  };
  img.onerror = () => {
    // Fallback gracefully to local sculpture image if Unsplash is unavailable offline
    if (img.src !== '/images/hero-3d-sculpture.webp') {
      img.src = '/images/hero-3d-sculpture.webp';
    }
  };
  img.src = s.src;

  const locate = e => {
    const rect = container.getBoundingClientRect();
    pointer.x = e.clientX - rect.left;
    pointer.y = e.clientY - rect.top;
    pointer.placed = true;
  };

  const onMove = e => {
    locate(e);
    if (!pointer.inside) {
      pointer.inside = true;
      pointer.fresh = true;
    }
    wake();
  };

  const onLeave = () => {
    pointer.inside = false;
    wake();
  };

  const onDown = e => {
    onMove(e);
    if (!s.clickBurst || (e.pointerType === 'mouse' && e.button !== 0)) return;
    bursts.push({ x: pointer.x, y: pointer.y, start: performance.now() });
    if (bursts.length > MAX_BURSTS) bursts.shift();
  };

  container.addEventListener('pointermove', onMove, { passive: true });
  container.addEventListener('pointerenter', onMove, { passive: true });
  container.addEventListener('pointerdown', onDown, { passive: true });
  container.addEventListener('pointerleave', onLeave, { passive: true });
  container.addEventListener('pointercancel', onLeave, { passive: true });

  const resizeObserver = new ResizeObserver(() => {
    applyLayout();
    wake();
  });
  resizeObserver.observe(container);

  const intersectionObserver = new IntersectionObserver(([entry]) => {
    visible = entry.isIntersecting;
    if (visible) wake();
  });
  intersectionObserver.observe(container);

  applyLayout();
  wake();

  return () => {
    alive = false;
    cancelAnimationFrame(raf);
    resizeObserver.disconnect();
    intersectionObserver.disconnect();
    img.onload = null;
    img.onerror = null;
    container.removeEventListener('pointermove', onMove);
    container.removeEventListener('pointerenter', onMove);
    container.removeEventListener('pointerdown', onDown);
    container.removeEventListener('pointerleave', onLeave);
    container.removeEventListener('pointercancel', onLeave);
    masks.forEach(destroyMask);
    if (canvas.parentNode) canvas.parentNode.removeChild(canvas);
  };
}

