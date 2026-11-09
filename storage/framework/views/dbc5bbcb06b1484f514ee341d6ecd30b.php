
<!DOCTYPE html>
<html lang="en-IN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="theme-color" content="#f7f7f5">

    
    <title><?php echo $__env->yieldContent('title', 'SAP Course Online: FICO, MM, SD, PP Training | ZebLearn'); ?></title>

    <meta
        name="description"
        content="<?php echo $__env->yieldContent('description', 'Learn SAP online with live classes, real system practice and placement support. Explore SAP FICO, MM, SD and PP courses at ZebLearn.'); ?>"
    >

    <meta
        name="robots"
        content="<?php echo $__env->yieldContent('robots', 'index, follow, max-image-preview:large'); ?>"
    >

    <link
        rel="canonical"
        href="<?php echo $__env->yieldContent('canonical', url()->current()); ?>"
    >

    
    <meta property="og:type" content="<?php echo $__env->yieldContent('og_type', 'website'); ?>">
    <meta property="og:site_name" content="ZebLearn">
    <meta property="og:locale" content="en_IN">

    <meta
        property="og:title"
        content="<?php echo $__env->yieldContent('og_title', 'SAP Training Courses | ZebLearn'); ?>"
    >

    <meta
        property="og:description"
        content="<?php echo $__env->yieldContent('og_description', 'Learn SAP with live online training, practical system experience and placement support.'); ?>"
    >

    <meta property="og:url" content="<?php echo e(url()->current()); ?>">

    <meta
        property="og:image"
        content="<?php echo $__env->yieldContent('og_image', asset('images/zeblearn-home-og.webp')); ?>"
    >

    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    
    <meta name="twitter:card" content="summary_large_image">

    <meta
        name="twitter:title"
        content="<?php echo $__env->yieldContent('twitter_title', 'SAP Training Courses | ZebLearn'); ?>"
    >

    <meta
        name="twitter:description"
        content="<?php echo $__env->yieldContent('twitter_description', 'Learn SAP with ZebLearn through live online training and practical learning.'); ?>"
    >

    <meta
        name="twitter:image"
        content="<?php echo $__env->yieldContent('og_image', asset('images/zeblearn-home-og.webp')); ?>"
    >

    
    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link
        rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin
    >

    <link
        href="https://fonts.googleapis.com/css2?family=Anton&family=Bricolage+Grotesque:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap"
        rel="stylesheet"
    >

    
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&display=swap"
        rel="stylesheet"
    >

    
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