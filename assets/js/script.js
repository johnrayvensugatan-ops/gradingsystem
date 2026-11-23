/**
 * Student Grading System - Interactive JavaScript
 * Real-time grade calculations, validations, and UI interactions
 */

document.addEventListener('DOMContentLoaded', function () {
    // 1. Live Grade Calculation Setup
    const prelimInput = document.getElementById('prelim');
    const midtermInput = document.getElementById('midterm');
    const prefinalInput = document.getElementById('prefinal');
    const finalInput = document.getElementById('final');

    const previewFinalGrade = document.getElementById('previewFinalGrade');
    const previewEquivalent = document.getElementById('previewEquivalent');
    const previewRemarks = document.getElementById('previewRemarks');

    function calculateLiveGrade() {
        if (!prelimInput || !midtermInput || !prefinalInput || !finalInput) return;

        const p = parseFloat(prelimInput.value) || 0;
        const m = parseFloat(midtermInput.value) || 0;
        const pf = parseFloat(prefinalInput.value) || 0;
        const f = parseFloat(finalInput.value) || 0;

        // Formula: 20% Prelim + 20% Midterm + 20% Pre-Final + 40% Final
        const finalGrade = (p * 0.20) + (m * 0.20) + (pf * 0.20) + (f * 0.40);
        const roundedGrade = Math.round(finalGrade * 100) / 100;

        let equivalent = '5.00';
        if (roundedGrade >= 97.00) equivalent = '1.00';
        else if (roundedGrade >= 94.00) equivalent = '1.25';
        else if (roundedGrade >= 91.00) equivalent = '1.50';
        else if (roundedGrade >= 88.00) equivalent = '1.75';
        else if (roundedGrade >= 85.00) equivalent = '2.00';
        else if (roundedGrade >= 82.00) equivalent = '2.25';
        else if (roundedGrade >= 79.00) equivalent = '2.50';
        else if (roundedGrade >= 76.00) equivalent = '2.75';
        else if (roundedGrade >= 75.00) equivalent = '3.00';
        else equivalent = '5.00';

        const remarks = (roundedGrade >= 75.00) ? 'Passed' : 'Failed';

        if (previewFinalGrade) previewFinalGrade.textContent = roundedGrade.toFixed(2) + '%';
        if (previewEquivalent) previewEquivalent.textContent = equivalent;
        if (previewRemarks) {
            previewRemarks.textContent = remarks;
            previewRemarks.className = 'badge ' + (remarks === 'Passed' ? 'bg-success' : 'bg-danger');
        }
    }

    // Attach listeners if inputs exist on the page
    if (prelimInput && midtermInput && prefinalInput && finalInput) {
        [prelimInput, midtermInput, prefinalInput, finalInput].forEach(input => {
            input.addEventListener('input', calculateLiveGrade);
        });
        // Initial run
        calculateLiveGrade();
    }

    // 2. Setup Delete Confirmation Modal
    const deleteModal = document.getElementById('deleteConfirmModal');
    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const recordId = button.getAttribute('data-id');
            const studentName = button.getAttribute('data-name');
            const subject = button.getAttribute('data-subject');

            const studentNameSpan = deleteModal.querySelector('#deleteStudentName');
            const deleteInputId = deleteModal.querySelector('#deleteRecordId');

            if (studentNameSpan) studentNameSpan.textContent = studentName + ' (' + subject + ')';
            if (deleteInputId) deleteInputId.value = recordId;
        });
    }

    // 3. Auto-dismiss Bootstrap flash alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(function (alert) {
        setTimeout(function () {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
            if (bsAlert) {
                bsAlert.close();
            }
        }, 5000);
    });
});
