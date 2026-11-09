
<!DOCTYPE html>
<html lang="en-IN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="theme-color" content="#f7f7f5">

    <?php
        $pageTitle = trim($__env->yieldContent('title', 'SAP Course Online: FICO, MM, SD, PP Training | ZebLearn'));
        $pageDesc = trim($__env->yieldContent('description', 'Learn SAP online with live classes, real system practice and placement support. Explore SAP FICO, MM, SD and PP courses at ZebLearn.'));
        $canonicalPath = request()->getPathInfo();
        $defaultCanonical = 'https://sap.zeblearn.com' . ($canonicalPath === '/' ? '/' : rtrim($canonicalPath, '/'));
    ?>

    
    <title><?php echo e($pageTitle); ?></title>

    <meta
        name="description"
        content="<?php echo e($pageDesc); ?>"
    >

    <meta
        name="robots"
        content="<?php echo $__env->yieldContent('robots', 'index, follow, max-image-preview:large'); ?>"
    >

    <link
        rel="canonical"
        href="<?php echo $__env->yieldContent('canonical', $defaultCanonical); ?>"
    >

    
    <meta property="og:type" content="<?php echo $__env->yieldContent('og_type', 'website'); ?>">
    <meta property="og:site_name" content="ZebLearn">
    <meta property="og:locale" content="en_IN">

    <meta
        property="og:title"
        content="<?php echo $__env->yieldContent('og_title', $pageTitle); ?>"
    >

    <meta
        property="og:description"
        content="<?php echo $__env->yieldContent('og_description', $pageDesc); ?>"
    >

    <meta property="og:url" content="<?php echo $__env->yieldContent('canonical', $defaultCanonical); ?>">

    <meta
        property="og:image"
        content="<?php echo $__env->yieldContent('og_image', 'https://sap.zeblearn.com/images/zeblearn-home-og.webp'); ?>"
    >

    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    
    <meta name="twitter:card" content="summary_large_image">

    <meta
        name="twitter:title"
        content="<?php echo $__env->yieldContent('twitter_title', $pageTitle); ?>"
    >

    <meta
        name="twitter:description"
        content="<?php echo $__env->yieldContent('twitter_description', $pageDesc); ?>"
    >

    <meta
        name="twitter:image"
        content="<?php echo $__env->yieldContent('og_image', 'https://sap.zeblearn.com/images/zeblearn-home-og.webp'); ?>"
    >

    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@500;600&family=Inter:wght@400;500;600;700&family=Manrope:wght@600;700;800&family=Mohave:wght@600;700&family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&display=swap">
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@500;600&family=Inter:wght@400;500;600;700&family=Manrope:wght@600;700;800&family=Mohave:wght@600;700&family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&display=swap" rel="stylesheet" media="print" onload="this.media='all'">
    <noscript>
        <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@500;600&family=Inter:wght@400;500;600;700&family=Manrope:wght@600;700;800&family=Mohave:wght@600;700&family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&display=swap" rel="stylesheet">
    </noscript>

    
    <?php echo app('Illuminate\Foundation\Vite')([
        'resources/css/app.css',
        'resources/js/app.js'
    ]); ?>

    
    <?php echo $__env->yieldPushContent('head'); ?>
    <?php echo $__env->yieldPushContent('schema'); ?>

</head>

<body class="<?php echo $__env->yieldContent('body-class', 'bg-surface text-on-surface font-body-md antialiased min-h-screen'); ?>">

    
    <a
        href="#main-content"
        class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-[100] focus:rounded-lg focus:bg-white focus:px-5 focus:py-3 focus:text-black focus:shadow-lg"
    >
        Skip to main content
    </a>

    
    <?php if (isset($component)) { $__componentOriginale30b2855ee1e4ae30e50fcbbc76a33ff = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale30b2855ee1e4ae30e50fcbbc76a33ff = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layout.header','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layout.header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale30b2855ee1e4ae30e50fcbbc76a33ff)): ?>
<?php $attributes = $__attributesOriginale30b2855ee1e4ae30e50fcbbc76a33ff; ?>
<?php unset($__attributesOriginale30b2855ee1e4ae30e50fcbbc76a33ff); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale30b2855ee1e4ae30e50fcbbc76a33ff)): ?>
<?php $component = $__componentOriginale30b2855ee1e4ae30e50fcbbc76a33ff; ?>
<?php unset($__componentOriginale30b2855ee1e4ae30e50fcbbc76a33ff); ?>
<?php endif; ?>

    
    <main id="main-content">
        <?php echo $__env->yieldContent('content'); ?>
    </main>

    
    <?php if (isset($component)) { $__componentOriginal4766510e0268a7a5917e77b146281554 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4766510e0268a7a5917e77b146281554 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layout.footer','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layout.footer'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal4766510e0268a7a5917e77b146281554)): ?>
<?php $attributes = $__attributesOriginal4766510e0268a7a5917e77b146281554; ?>
<?php unset($__attributesOriginal4766510e0268a7a5917e77b146281554); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal4766510e0268a7a5917e77b146281554)): ?>
<?php $component = $__componentOriginal4766510e0268a7a5917e77b146281554; ?>
<?php unset($__componentOriginal4766510e0268a7a5917e77b146281554); ?>
<?php endif; ?>

    
    <?php echo $__env->yieldPushContent('scripts'); ?>

</body>
</html>
<?php /**PATH /Users/ujjwalrai/Desktop/Office/Zeblearn/resources/views/layouts/app.blade.php ENDPATH**/ ?>