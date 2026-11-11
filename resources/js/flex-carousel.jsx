import React from 'react';
import { createRoot } from 'react-dom/client';
import FlexCarousel from '../reactItems/FlexCarousel';

export function initFlexCarousel() {
    const rootEl = document.getElementById('flex-carousel-root');
    if (!rootEl || rootEl._reactRoot) return;

    // Verified Student Placement Posters from /images/banner/orange/
    const items = [
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
        {
            src: '/images/banner/orange/ORANGE%201.webp',
            alt: 'ZebLearn Student Placement Record 01',
            title: 'ZebLearn Alumni',
            subtitle: 'SAP Consultant Placement',
            youtubeUrl: 'https://www.youtube.com/@ZEBLEARN'
        },
        {
            src: '/images/banner/orange/ORANGE%202.webp',
            alt: 'ZebLearn Student Placement Record 02',
            title: 'ZebLearn Alumni',
            subtitle: 'SAP Consultant Placement',
            youtubeUrl: 'https://www.youtube.com/@ZEBLEARN'
        },
        {
            src: '/images/banner/orange/ORANGE%203.webp',
            alt: 'ZebLearn Student Placement Record 03',
            title: 'ZebLearn Alumni',
            subtitle: 'SAP Consultant Placement',
            youtubeUrl: 'https://www.youtube.com/@ZEBLEARN'
        },
        {
            src: '/images/banner/orange/ORANGE%204.webp',
            alt: 'ZebLearn Student Placement Record 04',
            title: 'ZebLearn Alumni',
            subtitle: 'SAP Consultant Placement',
            youtubeUrl: 'https://www.youtube.com/@ZEBLEARN'
        },
        {
            src: '/images/banner/orange/ORANGE%205.webp',
            alt: 'ZebLearn Student Placement Record 05',
            title: 'ZebLearn Alumni',
            subtitle: 'SAP Consultant Placement',
            youtubeUrl: 'https://www.youtube.com/@ZEBLEARN'
        },
        {
            src: '/images/banner/orange/ORANGE%206.webp',
            alt: 'ZebLearn Student Placement Record 06',
            title: 'ZebLearn Alumni',
            subtitle: 'SAP Consultant Placement',
            youtubeUrl: 'https://www.youtube.com/@ZEBLEARN'
        },
        {
            src: '/images/banner/orange/ORANGE%207.webp',
            alt: 'ZebLearn Student Placement Record 07',
            title: 'ZebLearn Alumni',
            subtitle: 'SAP Consultant Placement',
            youtubeUrl: 'https://www.youtube.com/@ZEBLEARN'
        },
        {
            src: '/images/banner/orange/ORANGE%208.webp',
            alt: 'ZebLearn Student Placement Record 08',
            title: 'ZebLearn Alumni',
            subtitle: 'SAP Consultant Placement',
            youtubeUrl: 'https://www.youtube.com/@ZEBLEARN'
        },
        {
            src: '/images/banner/orange/ORANGE%209.webp',
            alt: 'ZebLearn Student Placement Record 09',
            title: 'ZebLearn Alumni',
            subtitle: 'SAP Consultant Placement',
            youtubeUrl: 'https://www.youtube.com/@ZEBLEARN'
        },
        {
            src: '/images/banner/orange/ORANGE%2010.webp',
            alt: 'ZebLearn Student Placement Record 10',
            title: 'ZebLearn Alumni',
            subtitle: 'SAP Consultant Placement',
            youtubeUrl: 'https://www.youtube.com/@ZEBLEARN'
        },
        {
            src: '/images/banner/orange/ORANGE%2011.webp',
            alt: 'ZebLearn Student Placement Record 11',
            title: 'ZebLearn Alumni',
            subtitle: 'SAP Consultant Placement',
            youtubeUrl: 'https://www.youtube.com/@ZEBLEARN'
        },
        {
            src: '/images/banner/orange/ORANGE%2012.webp',
            alt: 'ZebLearn Student Placement Record 12',
            title: 'ZebLearn Alumni',
            subtitle: 'SAP Consultant Placement',
            youtubeUrl: 'https://www.youtube.com/@ZEBLEARN'
        },
        {
            src: '/images/banner/orange/ORANGE%2013.webp',
            alt: 'ZebLearn Student Placement Record 13',
            title: 'ZebLearn Alumni',
            subtitle: 'SAP Consultant Placement',
            youtubeUrl: 'https://www.youtube.com/@ZEBLEARN'
        },
        {
            src: '/images/banner/orange/ORANGE%2014.webp',
            alt: 'ZebLearn Student Placement Record 14',
            title: 'ZebLearn Alumni',
            subtitle: 'SAP Consultant Placement',
            youtubeUrl: 'https://www.youtube.com/@ZEBLEARN'
        },
        {
            src: '/images/banner/orange/ORANGE%2015.webp',
            alt: 'ZebLearn Student Placement Record 15',
            title: 'ZebLearn Alumni',
            subtitle: 'SAP Consultant Placement',
            youtubeUrl: 'https://www.youtube.com/@ZEBLEARN'
        },
        {
            src: '/images/banner/orange/ORANGE%2016.webp',
            alt: 'ZebLearn Student Placement Record 16',
            title: 'ZebLearn Alumni',
            subtitle: 'SAP Consultant Placement',
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
