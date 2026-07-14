<?php
$title = 'Find the Perfect Gift in 30 Seconds';
$subtitle = 'SMART GIFT FINDER';
$desc = 'Tell us a little about them, and discover something thoughtful and memorable.';

if (!empty($section['content_json'])) {
    $content = json_decode($section['content_json'], true);
    $title = $content['title'] ?? $title;
    $subtitle = $content['subtitle'] ?? $subtitle;
    $desc = $content['description'] ?? $desc;
}
?>
<!-- gift finder widget -->
<div class="gift-finder-area pb-100 font-roboto">
    <div class="container">
        <!-- Flex Heading aligned left -->
        <div class="row align-items-center mb-4">
            <div class="col-12 text-start">
                <span class="badge mb-2 px-3 py-2 text-uppercase fw-bold text-danger bg-light" style="font-size: 0.8rem; border-radius: 50px; color: #ff3366 !important; letter-spacing: 1px;">
                    🎁 <?= esc($subtitle) ?>
                </span>
                <h2 class="fw-bold text-dark mt-1 mb-2" style="font-size: 2.3rem; letter-spacing: -0.5px;"><?= esc($title) ?></h2>
                <p class="text-muted mb-4" style="font-size: 1.05rem;"><?= esc($desc) ?></p>
            </div>
        </div>

        <!-- Wizard Container -->
        <div class="card border-0 shadow-sm p-4 p-md-5 rounded-4 bg-white" style="border-radius: 24px !important;">
            <!-- Step Header -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="fw-bold text-dark" style="font-size: 1.2rem;">
                    <span class="me-2 text-danger">🎁</span> Gift Finder
                </span>
                <span class="badge bg-light text-muted px-3 py-2 font-monospace" style="font-size: 0.9rem;" id="step-indicator">1/5</span>
            </div>

            <!-- Progress Bar -->
            <div class="progress mb-4" style="height: 6px; background-color: #f0f2f5; border-radius: 3px;">
                <div class="progress-bar bg-danger" role="progressbar" style="width: 20%; background-color: #ff3366 !important; transition: width 0.4s ease;" id="finder-progress"></div>
            </div>

            <!-- Wizard Step Panels -->
            <div class="wizard-steps-wrap py-3">
                <!-- Question Title -->
                <h4 class="fw-bold text-dark mb-4" id="step-question">For whom you are gifting?</h4>

                <!-- Options Row -->
                <div class="row g-3" id="step-options-container">
                    <!-- Options injected via JavaScript -->
                </div>
            </div>
            
            <!-- Navigation buttons -->
            <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                <button type="button" class="btn btn-outline-secondary px-4 py-2" id="finder-prev-btn" style="border-radius: 12px; display: none;">
                    <i class="fas fa-arrow-left me-1"></i> Back
                </button>
                <button type="button" class="btn btn-danger px-4 py-2 ms-auto" id="finder-next-btn" style="border-radius: 12px; background-color: #ff3366; border: none; display: none;">
                    Next <i class="fas fa-arrow-right ms-1"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const stepsData = [
        {
            question: "For whom you are gifting?",
            paramKey: "recipient",
            options: [
                { text: "For Her", icon: "👩", value: "for-her" },
                { text: "For Him", icon: "👨", value: "for-him" },
                { text: "Family & Couple", icon: "👨‍👩‍👧‍👦", value: "family" },
                { text: "Work & Professional", icon: "💼", value: "work" }
            ]
        },
        {
            question: "What is the occasion?",
            paramKey: "occasion",
            options: [
                { text: "Birthday", icon: "🎂", value: "birthday-gifts" },
                { text: "Anniversary", icon: "💑", value: "anniversary-gifts" },
                { text: "Love & Romance", icon: "💖", value: "gifts" },
                { text: "Celebration / Festival", icon: "🎉", value: "gifts" }
            ]
        },
        {
            question: "What is your budget?",
            paramKey: "budget",
            options: [
                { text: "Under ₹500", icon: "💸", value: "0-500" },
                { text: "₹500 - ₹1000", icon: "💵", value: "500-1000" },
                { text: "₹1000 - ₹2000", icon: "💰", value: "1000-2000" },
                { text: "Above ₹2000", icon: "💳", value: "2000-99999" }
            ]
        },
        {
            question: "What is their favorite vibe?",
            paramKey: "vibe",
            options: [
                { text: "Delicious Cakes", icon: "🍰", value: "cakes" },
                { text: "Fresh Flowers", icon: "🌹", value: "flowers" },
                { text: "Yummy Chocolates", icon: "🍫", value: "chocolates" },
                { text: "Green Plants", icon: "🍀", value: "plants" }
            ]
        },
        {
            question: "We found matching suggestions!",
            paramKey: "final",
            options: [
                { text: "Discover Perfect Gifts", icon: "✨", value: "submit" }
            ]
        }
    ];

    let currentStep = 0;
    const selections = {};

    const questionEl = document.getElementById("step-question");
    const containerEl = document.getElementById("step-options-container");
    const progressEl = document.getElementById("finder-progress");
    const indicatorEl = document.getElementById("step-indicator");
    const prevBtn = document.getElementById("finder-prev-btn");
    const nextBtn = document.getElementById("finder-next-btn");

    function renderStep() {
        const step = stepsData[currentStep];
        
        // Update texts & indicators
        questionEl.textContent = step.question;
        indicatorEl.textContent = `${currentStep + 1}/${stepsData.length}`;
        progressEl.style.width = `${((currentStep + 1) / stepsData.length) * 100}%`;

        // Render options
        containerEl.innerHTML = "";
        
        if (currentStep === stepsData.length - 1) {
            // Final step option as a huge button
            const opt = step.options[0];
            const div = document.createElement("div");
            div.className = "col-12 text-center py-4";
            div.innerHTML = `
                <p class="text-muted mb-4" style="font-size: 1.1rem;">Click the button below to display customized recommendations matching all your choices.</p>
                <button type="button" class="btn btn-danger btn-lg px-5 py-3 shadow" id="finder-submit-trigger" style="border-radius: 16px; background-color: #ff3366; border: none; font-weight: bold; font-size: 1.2rem;">
                    ${opt.icon} ${opt.text}
                </button>
            `;
            containerEl.appendChild(div);
            
            document.getElementById("finder-submit-trigger").addEventListener("click", submitFinder);
            nextBtn.style.display = "none";
        } else {
            // Regular multiple choice step options
            step.options.forEach(opt => {
                const isSelected = selections[step.paramKey] === opt.value;
                const col = document.createElement("div");
                col.className = "col-md-6 col-lg-3";
                col.innerHTML = `
                    <div class="choice-box p-4 text-center border rounded-4 bg-white shadow-sm" data-value="${opt.value}" style="border-radius: 16px; cursor: pointer; transition: all 0.25s ease; border: 1px solid ${isSelected ? '#ff3366' : '#e2e8f0'} !important; background-color: ${isSelected ? '#fff0f3' : '#ffffff'};">
                        <span class="d-block mb-3" style="font-size: 2.5rem;">${opt.icon}</span>
                        <span class="fw-bold text-dark text-capitalize" style="font-size: 1.1rem; color: ${isSelected ? '#ff3366' : '#2d3748'} !important;">${opt.text}</span>
                    </div>
                `;
                containerEl.appendChild(col);

                col.querySelector(".choice-box").addEventListener("click", function() {
                    selections[step.paramKey] = opt.value;
                    // Auto advance step
                    setTimeout(() => {
                        if (currentStep < stepsData.length - 1) {
                            currentStep++;
                            renderStep();
                        }
                    }, 200);
                });
            });
            nextBtn.style.display = "none"; // Hide next button to force options clicking
        }

        // Show/hide back button
        prevBtn.style.display = currentStep > 0 ? "block" : "none";
    }

    prevBtn.addEventListener("click", function() {
        if (currentStep > 0) {
            currentStep--;
            renderStep();
        }
    });

    function submitFinder() {
        let baseUrl = "<?= base_url('shop') ?>";
        const queryParams = [];
        
        // Map final choices
        if (selections.vibe) {
            // redirect to category page if vibe selected
            baseUrl = "<?= base_url('category') ?>/" + selections.vibe;
        } else if (selections.occasion) {
            baseUrl = "<?= base_url('category') ?>/" + selections.occasion;
        }

        if (selections.recipient) {
            queryParams.push("recipient=" + encodeURIComponent(selections.recipient));
        }
        if (selections.budget) {
            const range = selections.budget.split("-");
            queryParams.push("min_price=" + range[0]);
            queryParams.push("max_price=" + range[1]);
        }
        
        if (queryParams.length > 0) {
            baseUrl += "?" + queryParams.join("&");
        }

        window.location.href = baseUrl;
    }

    // Initial render
    renderStep();
});
</script>

<style>
.choice-box:hover {
    transform: translateY(-5px);
    border-color: #ff3366 !important;
    box-shadow: 0 8px 20px rgba(255, 51, 102, 0.08) !important;
}
.font-roboto {
    font-family: 'Roboto', sans-serif !important;
}
</style>
<!-- gift finder widget end -->
