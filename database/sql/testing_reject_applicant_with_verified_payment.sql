-- Local testing helper only.
-- Set this to the enrollment_applicants.id you want to test.
SET @applicant_id := 0;

-- Scenario: documents/registration rejected, but payment proof remains approved.
UPDATE enrollment_applicants
SET
    status = 'rejected',
    review_remarks = 'Registration details need correction. Please re-upload the rejected documents before resubmitting.',
    document_statuses = JSON_OBJECT(
        'photo_2x2', 'rejected',
        'birth_cert', 'approved',
        'report_card', 'rejected',
        'marriage_contract', 'pending',
        'medical_record', 'pending',
        'affidavit', 'pending'
    ),
    updated_at = NOW()
WHERE id = @applicant_id
  AND status IN ('ready_for_submission', 'pending', 'submitted', 'under_review');

UPDATE payments
SET
    status = 'verified',
    verified_at = COALESCE(verified_at, NOW()),
    updated_at = NOW()
WHERE enrollment_applicant_id = @applicant_id
  AND receipt_url IS NOT NULL;
