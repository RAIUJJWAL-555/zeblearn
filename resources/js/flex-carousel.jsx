import React from 'react';
import { createRoot } from 'react-dom/client';
import FlexCarousel from '../reactItems/FlexCarousel';

export function initFlexCarousel() {
    const rootEl = document.getElementById('flex-carousel-root');
    if (!rootEl || rootEl._reactRoot) return;

    // Verified Student Placement Posters from /images/banner/orange/
    const items = [
        {
            src: '/images/banner/orange/HAREE.webp',
            alt: 'Haree - Verified Placement Story',
            title: 'Haree',
            subtitle: 'Verified ZebLearn Placement',
            youtubeUrl: 'https://youtube.com/shorts/dGlDRVElzxQ'
        },
        {
            src: '/images/banner/orange/ARUP.webp',
            alt: 'Arup - Verified Placement Story',
            title: 'Arup',
            subtitle: 'Verified ZebLearn Placement',
            youtubeUrl: 'https://youtube.com/shorts/pK2aQX0mYvA'
        },
        {
            src: '/images/banner/orange/PRASHANT.webp',
            alt: 'Prashant - Verified Placement Story',
            title: 'Prashant',
            subtitle: 'Verified ZebLearn Placement',
            youtubeUrl: 'https://youtube.com/shorts/wKednd8TjSk'
        },
        {
            src: '/images/banner/orange/ALOKYADAV.webp',
            alt: 'Alok Yadav - Verified Placement Story',
            title: 'Alok Yadav',
            subtitle: 'Verified ZebLearn Placement',
            youtubeUrl: 'https://youtube.com/shorts/uGCTM3DloE4'
        },
        {
            src: '/images/banner/orange/ROCKEY.webp',
            alt: 'Rockey - Verified Placement Story',
            title: 'Rockey',
            subtitle: 'Verified ZebLearn Placement',
            youtubeUrl: 'https://youtube.com/shorts/D78El5X4_YM'
        },
        {
            src: '/images/banner/orange/NIRANKAR.webp',
            alt: 'Nirankar - Verified Placement Story',
            title: 'Nirankar',
            subtitle: 'Verified ZebLearn Placement',
            youtubeUrl: 'https://youtube.com/shorts/w-fXRXoOnXE'
        },
        {
            src: '/images/banner/orange/SHIVANI.webp',
            alt: 'Shivani - Verified Placement Story',
            title: 'Shivani',
            subtitle: 'Verified ZebLearn Placement',
            youtubeUrl: 'https://youtube.com/shorts/JVoNp6LS-cs'
        },
        {
            src: '/images/banner/orange/PRIYANSHU.webp',
            alt: 'Priyanshu - Verified Placement Story',
            title: 'Priyanshu',
            subtitle: 'Verified ZebLearn Placement',
            youtubeUrl: 'https://youtube.com/shorts/iG1d0RdLcU8'
        },
        {
            src: '/images/banner/orange/CHANDAN.webp',
            alt: 'Chandan - Verified Placement Story',
            title: 'Chandan',
            subtitle: 'Verified ZebLearn Placement',
            youtubeUrl: 'https://youtube.com/shorts/ljxsKPrp59c'
        },
        {
            src: '/images/banner/orange/PAVAN.webp',
            alt: 'Pawan - Verified Placement Story',
            title: 'Pawan',
            subtitle: 'Verified ZebLearn Placement',
            youtubeUrl: 'https://youtube.com/shorts/172tnka9L_g'
        },
        {
            src: '/images/banner/orange/PRAKASH.webp',
            alt: 'Prakash - Verified Placement Story',
            title: 'Prakash',
            subtitle: 'Verified ZebLearn Placement',
            youtubeUrl: 'https://youtube.com/shorts/r3-RYr7BrVw'
        },
        {
            src: '/images/banner/orange/SANJAY.webp',
            alt: 'Sanjay - Verified Placement Story',
            title: 'Sanjay',
            subtitle: 'Verified ZebLearn Placement',
            youtubeUrl: 'https://youtube.com/shorts/mpgTHfozeA8'
        },
        {
            src: '/images/banner/orange/SHUDHANSHU.webp',
            alt: 'Shudhanshu - Verified Placement Story',
            title: 'Shudhanshu',
            subtitle: 'Verified ZebLearn Placement',
            youtubeUrl: 'https://youtube.com/shorts/y_Lvpnh3P2U'
        },
        {
            src: '/images/banner/orange/PINTUKUMAR.webp',
            alt: 'Pintu Kumar - Verified Placement Story',
            title: 'Pintu Kumar',
            subtitle: 'Verified ZebLearn Placement',
            youtubeUrl: 'https://youtube.com/shorts/TdWVmbqDoXI'
        },
        {
            src: '/images/banner/orange/SHILPA.webp',
            alt: 'Shilpa - Verified Placement Story',
            title: 'Shilpa',
            subtitle: 'Verified ZebLearn Placement',
            youtubeUrl: 'https://youtube.com/shorts/TdWVmbqDoXI'
        },
        {
            src: '/images/banner/orange/AYUSH.webp',
            alt: 'Ayush - Verified Placement Story',
            title: 'Ayush',
            subtitle: 'Verified ZebLearn Placement',
            youtubeUrl: 'https://www.youtube.com/@ZEBLEARN'
        },
        {
            src: '/images/banner/orange/DIVYA%20SHREE.webp',
            alt: 'Divya Shree - Verified Placement Story',
            title: 'Divya Shree',
            subtitle: 'Verified ZebLearn Placement',
            youtubeUrl: 'https://www.youtube.com/@ZEBLEARN'
        },
        {
            src: '/images/banner/orange/INDERPREET.webp',
            alt: 'Inderpreet - Verified Placement Story',
            title: 'Inderpreet',
            subtitle: 'Verified ZebLearn Placement',
            youtubeUrl: 'https://www.youtube.com/@ZEBLEARN'
        },
        {
            src: '/images/banner/orange/POOJA.webp',
            alt: 'Pooja - Verified Placement Story',
            title: 'Pooja',
            subtitle: 'Verified ZebLearn Placement',
            youtubeUrl: 'https://www.youtube.com/@ZEBLEARN'
        },
        {
            src: '/images/banner/orange/PREETI.webp',
            alt: 'Preeti - Verified Placement Story',
            title: 'Preeti',
            subtitle: 'Verified ZebLearn Placement',
            youtubeUrl: 'https://www.youtube.com/@ZEBLEARN'
        },
        {
            src: '/images/banner/orange/SHAIK.webp',
            alt: 'Shaik - Verified Placement Story',
            title: 'Shaik',
            subtitle: 'Verified ZebLearn Placement',
            youtubeUrl: 'https://www.youtube.com/@ZEBLEARN'
        },
        {
            src: '/images/banner/orange/SHRUTI.webp',
            alt: 'Shruti - Verified Placement Story',
            title: 'Shruti',
            subtitle: 'Verified ZebLearn Placement',
            youtubeUrl: 'https://www.youtube.com/@ZEBLEARN'
        },
    ];

    const handleSelect = (_index, item) => {
        const url = item?.youtubeUrl || 'https://www.youtube.com/@ZEBLEARN';
        window.open(url, '_blank', 'noopener,noreferrer');
    };

    const root = createRoot(rootEl);
    rootEl._reactRoot = root;
    root.render(
        <div style={{ width: '100%', height: '560px', position: 'relative' }}>
            <FlexCarousel
                items={items}
                preset="liquid"
                intro="rise"
                cardHeight={0.52}
                gap={14}
                squeeze={0.2}
                focusOnClick
                captions
                fit="natural"
                radius={16}
                lensWidth={0.74}
                lensHeight={1.18}
                tilt={62}
                roundness={1}
                bend={0.34}
                reach={0.38}
                curl="twist"
                dispersion={0.45}
                liquid={0}
                followCursor={false}
                autoplay={true}
                interval={3.5}
                captureWheel
                onSelect={handleSelect}
            />
        </div>
    );
}
