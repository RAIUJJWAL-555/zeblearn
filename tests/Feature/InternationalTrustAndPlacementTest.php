<?php

test('homepage renders all 5 international trust and placement experience sections in the exact required order', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
    $html = $response->getContent();

    // 1. SECTION 1: LEARNERS FROM AROUND THE WORLD
    $response->assertSee('LEARNERS FROM AROUND THE WORLD');
    $response->assertSee('id="international-learners"', false);
    $response->assertSee('id="intl-carousel-track"', false);
    $response->assertSee('United Arab Emirates');
    $response->assertSee('Saudi Arabia');
    $response->assertSee('South Africa');
    $response->assertSee('Oman');
    $response->assertSee('Malaysia');
    $response->assertSee('Fahad Al-Mansoori');
    $response->assertSee('Tariq Al-Harbi');
    $response->assertSee('Kagiso Ndlovu');

    // 2. SECTION 2: PLACEMENT PROOF & VERIFICATION
    $response->assertSee("DON'T BELIEVE US? VERIFY EVERYTHING.", false);
    $response->assertSee('id="placement-proof"', false);
    $response->assertSee('id="proof-modal"', false);
    $response->assertSee('Accenture');

    // 3. SECTION 3: OUR PROVEN 7-STEP PLACEMENT PROCESS
    $response->assertSee('OUR PROVEN 7-STEP PLACEMENT PROCESS');
    $response->assertSee('id="placement-process"', false);
    $response->assertSee('id="process-carousel-track"', false);
    $response->assertSee('Career Counselling &amp; Profile Evaluation', false);
    $response->assertSee('Expert-Led Live Training');
    $response->assertSee('Real Projects &amp; Sandbox Practice', false);
    $response->assertSee('Resume Building');
    $response->assertSee('Mock Interviews');
    $response->assertSee('Interview Preparation &amp; Referrals', false);
    $response->assertSee('Career Opportunities via PlacementBaba');

    // 4. SECTION 4: VERIFIED STUDENT REVIEWS
    $response->assertSee('WHAT OUR');
    $response->assertSee('STUDENTS SAY');
    $response->assertSee('id="verified-reviews"', false);
    $response->assertSee('id="reviews-carousel-track"', false);
    $response->assertSee('4.5');
    $response->assertSee('58,765+ Google reviews');
    $response->assertSee('Sulekha');
    $response->assertSee('Urbanpro');
    $response->assertSee('JustDial');
    $response->assertSee('Ganesh');
    $response->assertSee('Ayesha Khan');
    $response->assertSee('Rohit Gupta');
    $response->assertSee('Sumit Singh');
    $response->assertSee('Placed at Accenture');

    // 5. SECTION 5: VIDEO STORIES FROM OUR STUDENTS
    $response->assertSee('FEEDBACK FROM OUR STUDENTS');
    $response->assertSee('HEAR FROM OUR STUDENTS');
    $response->assertSee('id="stories"', false);
    $response->assertSee('id="video-carousel"', false);
    $response->assertSee('id="yt-modal"', false);

    // Verify Exact Section Ordering in HTML:
    // 1. method -> international-learners -> awards-and-recognition -> placement-proof -> placement-process -> verified-reviews -> stories
    $posMethod = strpos($html, 'id="method"');
    $posIntl = strpos($html, 'id="international-learners"');
    $posAwards = strpos($html, 'id="awards-and-recognition"');
    $posProof = strpos($html, 'id="placement-proof"');
    $posProcess = strpos($html, 'id="placement-process"');
    $posReviews = strpos($html, 'id="verified-reviews"');
    $posStories = strpos($html, 'id="stories"');

    expect($posMethod)->toBeLessThan($posIntl)
        ->and($posIntl)->toBeLessThan($posAwards)
        ->and($posAwards)->toBeLessThan($posProof)
        ->and($posProof)->toBeLessThan($posProcess)
        ->and($posProcess)->toBeLessThan($posReviews)
        ->and($posReviews)->toBeLessThan($posStories);
});
