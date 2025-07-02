<?php
namespace App\Services;
use DateTime;
use Carbon\Carbon;

class DocumentGeneratorService
{
    public function generateHtmlDocument($documentType, $patient, $doctor, $formData = [], $autoPrint = false)
    {
        $templateData = $this->prepareTemplateData($patient, $doctor, $formData);
        
        $documentContent = $this->getDocumentTemplate($documentType, $templateData);
        
        return $this->wrapInHtmlLayout($documentContent, $documentType, $autoPrint);
    }

    
    protected function prepareTemplateData($patient, $doctor, $formData)
    {
        return [
            // Patient data
            'patient_name' => $patient->name ?? '',
            'patient_surname' => $patient->surname ?? '',
            'patient_full_name' => ($patient->name ?? '') . ' ' . ($patient->surname ?? ''),
            'patient_dob' => $patient->date_of_birth ?? '',
            'patient_age' => $this->calculateAge($patient->date_of_birth),
            'patient_sex' => $patient->sex ?? '',
            'patient_gender_title' => $this->getGenderTitle($patient->sex),
            'patient_cin' => $patient->CIN ?? '',
            'patient_phone' => $patient->phone ?? '',
            'patient_address' => $patient->address ?? '',
            'patient_uid' => $patient->uid ?? '',
            'patient_blood_type' => $patient->blood_type ?? '',
            'patient_coverage' => $patient->coverage ? 'Couvert' : 'Non Couvert',
            'patient_coverage_type' => $patient->coverage_type ?? '',
            
            // Doctor data
            'doctor_name' => $doctor->name ?? '',
            'doctor_full_name' => ($doctor->first_name ?? '') . ' ' . ($doctor->last_name ?? ''),
            'doctor_speciality' => $doctor->speciality ?? 'Médecin Généraliste',
            'doctor_phone' => $doctor->phone ?? '',
            'doctor_email' => $doctor->email ?? '',
            'doctor_address' => $doctor->address ?? '',
            'clinic_name' => $doctor->clinic_name ?? '',
            'clinic_address' => $doctor->clinic_address ?? '',
            
            // Common data
            'current_date' => Carbon::now()->format('d/m/Y'),
            'current_datetime' => Carbon::now()->format('d/m/Y à H:i'),
            'current_time' => Carbon::now()->format('H:i'),
            'current_year' => Carbon::now()->year,
            
            // Form data
            'form_data' => $formData
        ];
    }

    protected function getDocumentTemplate($documentType, $data)
    {
        switch ($documentType) {
            case 'certificat-medical-aptitude-fr':
                return $this->certificatAptitudeTemplate($data);
            
            case 'certificat-arret-travail':
                return $this->certificatArretTravailTemplate($data);
            
            case 'certificat-medical-at':
                return $this->certificatMedicalATTemplate($data);
            
            case 'at-wafa':
                return $this->atWafaTemplate($data);
            
            case 'avp-cie':
                return $this->avpCieTemplate($data);
            
            case 'lettre-assurance':
                return $this->lettreAssuranceTemplate($data);
            
            case 'certificat-avp-initial':
                return $this->certificatAvpInitialTemplate($data);
            
            case 'certificat-avp-consolidation':
                return $this->certificatAvpConsolidationTemplate($data);
            
            case 'facturation':
                return $this->facturationTemplate($data);
            case 'certificat-aptitude-en':
                return $this->certificatAptitudeEnTemplate($data);

            case 'certificat-mariage-ar':
                return $this->certificatMariageTemplate($data);

            case 'certificat-mariage-ar-x01':
                return $this->certificatMariageTemplate_x01($data);

            case 'lettre-avocat':
                return $this->convocationAssuranceTemplate($data);

            case 'insurance-letter-ar':
                return $this->avisExpertiseArTemplate($data);

            case 'medical-fitness-ar':
                return $this->certificatAptitudeArTemplate($data);
            
            default:
                return $this->genericTemplate($data, $documentType);
        }
    }

    protected function certificatAptitudeTemplate($data) 
    {
        $f = $data['form_data'];
        $notes = $f['notes'] ?? "Constate, d’après l’examen clinique, qu’il/elle est indemne de toute affection contagieuse, invalidante, cardiaque ou mentale.";
        
        $dobRaw = $data['patient_dob'];
        $dobDate = DateTime::createFromFormat('d/m/Y', $dobRaw) ?: DateTime::createFromFormat('Y-m-d', $dobRaw);
        $dob = $dobDate ? $dobDate->format('d/m/Y') : '??';

        $today = date('d/m/Y');
    
        return "
        <div style='
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
            min-height: 90vh;
            font-family: Arial, sans-serif;
            font-size: 13px;
            padding: 0 10px;
        '>
            <div style='max-width: 460px; width: 100%;'>
                <div style='margin-bottom:30px'>
                    <h3 style='text-align: center; margin: 4px 0;'>CERTIFICAT MÉDICAL D’APTITUDE PHYSIQUE</h3>
                </div>
    
                <p style='text-align: center; margin: 10px 0 4px;'><strong>EXAMEN MÉDICAL</strong></p>
                <p style='margin: 4px 0; text-align: justify;margin-top: 20px;'>
                    Je soussigné, Dr AbdelFettah Idrissi Kaitouni, expert assermenté, certifie avoir examiné en ce jour du <strong>{$today}</strong> :
                    <strong>{$data['patient_full_name']}</strong>, né(e) le <strong>{$dob}</strong>, titulaire de la carte d'immatriculation n° <strong>{$f['carte']}</strong>.
                </p>
    
                <p style='margin: 10px 0 4px; text-align: justify;'>
                    {$notes}
                </p>
            </div>
        </div>
    ";
     }
    
    protected function certificatAptitudeEnTemplate($data)
    {
        $f = $data['form_data'];
        $today = date('d/m/Y');
    
        $html = "
            <div style='
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100%;
                min-height: 90vh;
                font-family: Arial, sans-serif;
                font-size: 13px;
                padding: 0 10px;
            '>
                <div style='max-width: 460px; width: 100%;'>
                    <h3 style='text-align: center; margin: 4px 0;margin-bottom: 25px;'>CERTIFICATE OF PHYSICAL FITNESS MEDICAL</h3>
    
                    <p style='margin: 10px 0;'>I UNDERSIGNED CERTIFIES HAVING REVIEWED SO FAR</p>
                    
                    <p><strong>MR / MISS:</strong> {$data['patient_full_name']}</p>
                    <p><strong>CIN:</strong> {$data['patient_cin']}</p>
    
                    <p style='margin-top: 20px;'>
                        AND NOTES ACCORDING TO THE PSYCHOSOMATIC EXAM THAT HE/SHE IS
                        <strong>{$f['fitness_type']}</strong>
                        FROM ANY CONTAGIOUS DISEASE, DISABLING, HEART OR MENTAL CONDITION.
                    </p>";
    
        if (!empty($f['restrictions']) && strtolower($f['fitness_type']) === 'fit with restrictions') {
            $html.= "<p style='margin-top: 10px;'><strong>Restrictions:</strong><br>{$f['restrictions']}</p>";
        }
    
        $html.= "
                    <br><br>
                    <p style='text-align: center;'>". (!empty($f['footer_note'])? nl2br(htmlspecialchars($f['footer_note'])) : "&nbsp;"). "</p>
                    <p style='text-align: center; margin-top: 40px;'>Casablanca, {$today}</p>
                </div>
            </div>
        ";
        
        return $html;
    }
    protected function certificatMariageTemplate($data)
    {
        $f = $data['form_data'];
        $notes = $f['notes'] ?? "إذن فهي قادرة على الزواج من الناحية الصحية.";
        
        return "
        <div style='direction: rtl; font-family: \"Arial\", sans-serif; font-size: 15px; padding: 10px; line-height: 1.8;'>
            <div style='text-align: center; margin-bottom: 20px;margin-bottom: 80px;'>
                <h3>الدكتور عبد الفتاح إدريسي  قيطوني</h3>
                <p>خبير محلف لدى المحاكم</p>
                <p>الطب العام</p>
                <p>الفحص بالصدى</p>
                <p>الهاتف : 0522932213</p>
            </div>
    
            <h2 style='text-align: center;padding-bottom: 23px;'>
            خبرة طبية للزواج
            </h2>
       
            <p>
                أنا الموقع أسفله الدكتور عبدالفتاح إدريسي قيطوني، خبير محلف لدى المحاكم، أشهد أني فحصت يومه
                <strong>{$f['date_exam']}</strong>
                بطلب من محكمة قسم الأسرة بالبيضاء.
            </p>
    
            <p style='margin-top: 15px;'><strong>فحص المرشحة للزواج: <strong>{$f['patient_name_ar']}</strong> </strong></p>
    
            <p>
                يبين الفحص السريري أن المعنية بالأمر 
                قادرة من الناحية الصحية على الممارسة الجنسية مع زوجها وعلى الوطء وعلى تحمل المسؤولية الزوجية وعلى الولادة.
            </p>
    
            <b style='margin-top: 20px;'>{$notes}</b>
        </div>
        ";
    }
    protected function certificatMariageTemplate_x01($data)
    {
        $f = $data['form_data'];
        $name = $f['patient_name_ar'];
        $sex = $f['sex'];
        $date_exam = $f['date_exam'];
        $notes = $f['notes'] ?? "";
    
        // Gender-based logic
        if ($sex === 'أنثى') {
            $requested_by = "بطلب منها المعنية";
            $pronoun = "المعنية بالأمر لا تظهر عليها علامة لمرض معدٍ";
            $delivered_to = "وسلمت لها هذه الشهادة للإدلاء بها قصد الزواج.";
        } else {
            $requested_by = "بطلب منه المعني";
            $pronoun = "المعني بالأمر لا تظهر عليه علامة لمرض معدٍ";
            $delivered_to = "وسلمت له هذه الشهادة للإدلاء بها قصد الزواج.";
        }
    
        return "
        <div style='direction: rtl; font-family: \"Arial\", sans-serif; font-size: 15px; padding: 10px; line-height: 1.8;'>
            <div style='text-align: center; margin-bottom: 80px;'>
                <h3>الدكتور عبد الفتاح إدريسي قيطوني</h3>
                <p>خبير محلف لدى المحاكم</p>
                <p>الطب العام</p>
                <p>الفحص بالصدى</p>
                <p>الهاتف : 0522932213</p>
            </div>
    
            <h2 style='text-align: center; padding-bottom: 23px;'>شهادة طبية قصد الزواج</h2>
    
            <p>
                أنا الموقع أسفله الدكتور عبد الفتاح إدريسي قيطوني، أشهد أني فحصت يومه <strong> {$date_exam}</strong> 
                {$requested_by} <strong>{$name}</strong>
            </p>
               
    
            <p style='margin-top: 15px;'>
                وتبين بعد الفحص السريري أن {$pronoun}.
            </p>
    
            <p style='margin-top: 15px;'>
                {$delivered_to}
            </p>
    
            " . (!empty($notes) ? "<p style='margin-top: 20px; font-weight: bold;'>{$notes}</p>" : "") . "
        </div>
        ";
    }
    
    
protected function convocationCourAppelTemplate($data)
{
    $f = $data['form_data'];

    return "
        <div style='font-family: Arial, sans-serif; font-size: 14px; padding: 10px; line-height: 1.6;'>
            <p style='text-align: center; font-weight: bold;'>COUR D’APPEL DE CASABLANCA</p>

            <p><strong>MONSIEUR :</strong> {$f['patient_name']}</p>
            <p><strong>DOSSIER COUR D’APPEL :</strong> {$f['dossier_cour']}</p>
            <p><strong>DOSSIER TPI CASA :</strong> {$f['dossier_tpi']}</p>
            <p><strong>JUGEMENT :</strong> {$f['date_jugement']}</p>
            <p><strong>AVP DU :</strong> {$f['date_avp']}</p>

            <p style='margin-top: 20px;'>
                Conformément à l’article 63 du code de procédure civile,<br>
                Je me permets de vous informer que l’expertise sur <strong>{$f['objet_expertise']}</strong><br>
                est prévue le <strong>{$f['date_expertise']}</strong> à <strong>{$f['heure']}</strong>.
            </p>

            <p style='margin-top: 20px;'>Je vous prie de bien vouloir y assister.</p>
        </div>
    ";
}

    protected function certificatArretTravailTemplate($data)
    {
        $f = $data['form_data'];
        $start = new \DateTime($f['date_debut']);
        $end = new \DateTime($f['date_fin']);
        $days = $start->diff($end)->days + 1;
    
        return "
            <div style='
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100%;
                min-height: 90vh;
                font-family: Arial, sans-serif;
                font-size: 13px;
                padding: 0 10px;
            '>
                <div style='max-width: 460px; width: 100%;'>
                    <h3 style='text-align: center; margin: 4px 0;margin-bottom: 25px;'>CERTIFICAT MÉDICAL D’ARRÊT DE TRAVAIL</h3>
    
                    <p>
                        JE SOUSSIGNÉ DOCTEUR ABDELFETTAH IDRISSI KAITOUNI, MÉDECIN ASSERMENTÉ PRÈS LES TRIBUNAUX, CERTIFIE QUE L'ÉTAT DE SANTÉ DE :
                    </p>
    
                    <p><strong>{$data['patient_full_name']}</strong></p>
    
                    <p>NÉCESSITE UN ARRÊT DE TRAVAIL DE <strong>{$days} JOURS</strong> DU <strong>{$start->format('d/m/Y')}</strong> AU <strong>{$end->format('d/m/Y')}</strong> INCLUS.</p>
                    <p>SAUF COMPLICATIONS.</p>
                </div>
            </div>
        ";
    }

    protected function facturationTemplate($data)
    {
        return "
            <div class='document-header'>
                <h2>FACTURE MÉDICALE</h2>
            </div>
            
            <div class='doctor-info'>
                <strong>Dr. {$data['doctor_full_name']}</strong><br>
                {$data['doctor_speciality']}<br>
                {$data['clinic_address']}<br>
                Tél: {$data['doctor_phone']}
            </div>
            
            <div class='document-body'>
                <div class='patient-info'>
                    <h3>Patient:</h3>
                    <p><strong>{$data['patient_gender_title']} {$data['patient_full_name']}</strong></p>
                    <p>Dossier N°: {$data['patient_uid']}</p>
                    <p>Date: {$data['current_date']}</p>
                </div>
                
                <table class='facture-table'>
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th>Quantité</th>
                            <th>Prix unitaire</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Consultation médicale</td>
                            <td>1</td>
                            <td>200.00 DH</td>
                            <td>200.00 DH</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan='3'><strong>Total à payer:</strong></td>
                            <td><strong>200.00 DH</strong></td>
                        </tr>
                    </tfoot>
                </table>
                
                <div class='signature-section'>
                    <p>Fait à Casablanca, le {$data['current_date']}</p>
                    <br><br>
                    <p><strong>Dr. {$data['doctor_full_name']}</strong></p>
                    <p>Signature et cachet</p>
                </div>
            </div>
        ";
    }

    protected function genericTemplate($data, $documentType)
    {
        return "
            <div class='document-header'>
                <h2>DOCUMENT MÉDICAL</h2>
            </div>
            
            <div class='doctor-info'>
                <strong>Dr. {$data['doctor_full_name']}</strong><br>
                {$data['doctor_speciality']}<br>
                {$data['clinic_address']}<br>
                Tél: {$data['doctor_phone']}
            </div>
            
            <div class='document-body'>
                <div class='patient-info'>
                    <p><strong>{$data['patient_gender_title']} {$data['patient_full_name']}</strong></p>
                    <p>Né(e) le: {$data['patient_dob']}</p>
                    <p>CIN: {$data['patient_cin']}</p>
                    <p>Dossier N°: {$data['patient_uid']}</p>
                </div>
                
                <p>Document généré le {$data['current_date']}</p>
                
                <div class='signature-section'>
                    <br><br>
                    <p><strong>Dr. {$data['doctor_full_name']}</strong></p>
                    <p>Signature et cachet</p>
                </div>
            </div>
        ";
    }

    protected function wrapInHtmlLayout($content, $documentType, $autoPrint = false)
    {
        $autoPrintScript = $autoPrint ? "
            <script>
                window.onload = function() {
                    setTimeout(function() {
                        window.print();
                    }, 500);
                };
            </script>
        " : "";

        return "
        <!DOCTYPE html>
        <html lang='fr'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Document Médical</title>
            <style>
                @page {
                    size: A5;
                    
                }
                
                * {
                    margin: 0;
                    padding: 0;
                    box-sizing: border-box;
                    font-size: 18px !important;
                }
                
                body {
                    font-family: 'Arial', sans-serif;
                    font-size: 12px;
                    line-height: 1.4;
                    color: #333;
                    background: white;
                }
                
                .document-header {
                    text-align: center;
                    margin-bottom: 20px;
                    border-bottom: 2px solid #333;
                    padding-bottom: 10px;
                }
                
                .document-header h2 {
                    font-size: 16px;
                    font-weight: bold;
                    text-transform: uppercase;
                }
                
                .doctor-info {
                    text-align: right;
                    margin-bottom: 20px;
                    font-size: 11px;
                    border: 1px solid #ddd;
                    padding: 10px;
                    background-color: #f9f9f9;
                }
                
                .document-body {
                    margin-top: 10px !important;
                    padding-top : 50px !important;
                }
                
                .patient-info {
                    background-color: #f5f5f5;
                    padding: 10px;
                    margin: 15px 0;
                    border-left: 4px solid #007bff;
                }
                
                .patient-info p {
                    margin: 5px 0;
                }
                
                .arret-details {
                    margin: 15px 0;
                    padding: 10px;
                    background-color: #fff3cd;
                    border: 1px solid #ffeaa7;
                }
                
                .signature-section {
                    margin-top: 30px;
                    text-align: right;
                }
                
                .facture-table {
                    width: 100%;
                    border-collapse: collapse;
                    margin: 20px 0;
                }
                
                .facture-table th,
                .facture-table td {
                    border: 1px solid #ddd;
                    padding: 8px;
                    text-align: left;
                }
                
                .facture-table th {
                    background-color: #f2f2f2;
                    font-weight: bold;
                }
                
                .facture-table tfoot td {
                    background-color: #f9f9f9;
                    font-weight: bold;
                }
                
                p {
                    margin: 8px 0;
                }
                
                strong {
                    font-weight: bold;
                }
                
                @media print {
                    body {
                        font-size: 11px;
                    }
                    
                    .no-print {
                        display: none;
                    }
                }
            </style>
            $autoPrintScript
        </head>
        <body>
            $content
        </body>
        </html>
        ";
    }

    protected function calculateAge($dateOfBirth)
    {
        if (!$dateOfBirth) return '';
        return Carbon::parse($dateOfBirth)->age;
    }

    protected function getGenderTitle($sex)
    {
        switch ($sex) {
            case 'M':
                return 'M.';
            case 'F':
                return 'Mme';
            case 'Mlle':
                return 'Mlle';
            default:
                return '';
        }
    }
    protected function atWafaTemplate($data)
{
    $f = $data['form_data'];

    return "
        <div class='document-body' >
            <p style='text-align: right;font-size:13px !important'>Casablanca, le {$f['date_examen']}</p>

            <p style='font-size:13px !important'>
                o TRIBUNAL DE 1ERE INSTANCE DE CASABLANCA<br>
                o DOSSIER N° : ../../….<br>
                o JUGEMENT DU : ../../….<br>
                o AT : {$f['date_accident']}<br>
                o VICTIME : {$data['patient_full_name']}<br>
                o CIN : {$data['patient_cin']}<br>
                o ASSURANCE : WAFA
            </p>

            <h3 style='text-align: center; margin: 20px 0;'>RAPPORT D’EXPERTISE MEDICALE</h3>

            <p>Je soussigné DR Abdelfettah IDRISSI KAITOUNI Expert Assermenté par les Tribunaux certifie avoir examiné le {$f['date_examen']} MONSIEUR {$data['patient_full_name']}, né le {$data['patient_dob']}, victime d’un AT le {$f['date_accident']} à {$f['lieu_accident']}.</p>

            <p><strong>DOCUMENTS :</strong></p>
            <ul>
                <li>
                    Un certificat initial établi le {$f['date_certificat_initial']} par DR {$f['medecin_initial']} note :
                    <ul>
                        <li>{$f['description']}</li>
                        <li>ITT : {$f['itt']} jours.</li>
                    </ul>
                </li>
                <li>
                    Un certificat de consolidation le {$f['date_consolidation']} par DR {$f['medecin_consolidation']} avec IPP de {$f['ipp']}%.
                </li>
            </ul>

            <p><strong>EXAMEN CLINIQUE ACTUEL :</strong></p>
            <div style='margin-left: 20px;'>
                <p>{$f['examen_clinique']}</p>
            </div>

            <p><strong>CONCLUSION :</strong></p>
            <ul>
                <li>+ ITT : {$f['itt']} JOURS.</li>
                <li>+ IPP : {$f['ipp']}% (".intval($f['ipp'])." pour cent).</li>
                <li>+ Date de consolidation : {$f['date_consolidation_finale']}</li>
            </ul>
        </div>
    ";
}

    
protected function certificatMedicalAtTemplate($data)  
{
    $f = $data['form_data'];

    return "
        <div style='
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
            min-height: 90vh;
            font-family: Arial, sans-serif;
            font-size: 13px;
            padding: 0 10px;
        '>
            <div style='max-width: 460px; width: 100%;'>
                <div style='margin-bottom:30px'>
                <h3 style='text-align: center; margin: 4px 0;'>CERTIFICAT MÉDICAL D’ACCIDENT</h3>
                <h4 style='text-align: center; margin: 4px 0;'>CONSOLIDATION + IPP</h4>
                </div>

                <p style='margin: 6px 0;'><strong>NOM & PRÉNOM :</strong> {$data['patient_full_name']}</p>
                <p style='margin: 6px 0;'><strong>ACCIDENT DE TRAVAIL DU :</strong> {$f['date_accident']}</p>
                <p style='margin: 6px 0;'><strong>ASSURANCE :</strong> {$f['assurance']}</p>
                <p style='margin: 6px 0;'><strong>EMPLOYEUR :</strong> {$data['patient_full_name']}</p>

                <p style='text-align: center; margin: 10px 0 4px;'><strong>DIAGNOSTIC</strong></p>
                <p style='margin: 0 0 8px;'>{$f['diagnostic']}</p>

                <p style='text-align: center; margin: 10px 0 4px;'><strong>CONCLUSION</strong></p>
                <p style='margin: 4px 0;'><strong>IPP :</strong> {$f['ipp']}%</p>
                <p style='margin: 4px 0;'><strong>Consolidation le :</strong> {$f['date_consolidation']}</p>

            </div>
        </div>
    ";
}





protected function avpCieTemplate($data)
{
    $f = $data['form_data'];

    return "
        <div class='document-body'>
            <p style='text-align: right; font-size: 13px !important'>Casablanca, le {$f['date_examen']}</p>

            <p style='font-size: 13px !important'>
                o TRIBUNAL DE 1ERE INSTANCE DE CASABLANCA<br>
                o DOSSIER N° : " . ($f['numero_dossier'] ?? '…/…/…') . "<br>
                o JUGEMENT DU : {$f['date_jugement']}<br>
                o AVP : {$f['date_accident']}<br>
                o VICTIME : {$data['patient_full_name']}<br>
                o CIN : {$data['patient_cin']}<br>
                o COMPAGNIE D'ASSURANCES : {$f['compagnie']}
            </p>

            <h3 style='text-align: center; margin: 20px 0;'>RAPPORT D’EXPERTISE MEDICALE</h3>

            <p>Je soussigné DR Abdelfettah IDRISSI KAITOUNI Expert Assermenté par les Tribunaux certifie avoir examiné le {$f['date_examen']} à 11 heures à la demande du tribunal régional, MONSIEUR {$data['patient_full_name']}, né le {$data['patient_dob']}, victime d’un AVP le {$f['date_accident']}.</p>

            <p><strong>DOCUMENTS :</strong></p>
            <ul>
                <li>
                    Un certificat initial établi le {$f['date_certificat_initial']} par DR {$f['medecin_initial']} note :
                    <ul>
                        " . (isset($f['notes']) && is_array($f['notes']) ? implode('', array_map(fn($note) => "<li>$note</li>", $f['notes'])) : "") . "
                    </ul>
                </li>
                <li>
                    Consolidation le {$f['date_consolidation']} par DR {$f['medecin_consolidation']} avec IPP de {$f['ipp']}%.
                </li>
            </ul>

           <p><strong>EXAMEN CLINIQUE ACTUEL :</strong></p>
            <div style='margin-left: 20px;'>
                <p>{$f['examen_clinique']}</p>
            </div>

            <ul>
                " . (isset($f['examen_clinique']) && is_array($f['examen_clinique']) ? implode('', array_map(fn($item) => "<li>$item</li>", $f['examen_clinique'])) : "") . "
            </ul>

            <p><strong>CONCLUSION :</strong></p>
            <ul>
                <li>+ ITT : {$f['itt']} jours.</li>
                <li>+ IPP : {$f['ipp']}% (".intval($f['ipp'])." pour cent)</li>
                <li>+ PRETIUM DOLORIS : {$f['pretium_doloris']}</li>
                <li>+ PRÉJUDICE ESTHÉTIQUE : {$f['prejudice_esthetique']}</li>
                <li>+ PRÉJUDICE PROFESSIONNEL : {$f['prejudice_professionnel']}</li>
            </ul>
        </div>
    ";
}


protected function lettreAssuranceTemplate($data) 
{
    $f = $data['form_data'];

    return "
        <div style='
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
            min-height: 90vh;
            font-family: Arial, sans-serif;
            font-size: 14px;
            padding: 0 10px;
        '>
            <div style='max-width: 460px; width: 100%; line-height: 1.6;'>
                <h3 style='text-align: center; font-weight: bold; margin-bottom: 20px;'>COUR D’APPEL DE CASABLANCA</h3>

                <p style='margin: 6px 0;'><strong>MONSIEUR :</strong> {$f['patient_name']}</p>
                <p style='margin: 6px 0;'><strong>DOSSIER COUR D’APPEL :</strong> {$f['dossier_cour']}</p>
                <p style='margin: 6px 0;'><strong>DOSSIER TPI CASA :</strong> {$f['dossier_tpi']}</p>
                <p style='margin: 6px 0;'><strong>JUGEMENT :</strong> {$f['date_jugement']}</p>
                <p style='margin: 6px 0;'><strong>AVP DU :</strong> {$f['date_avp']}</p>

                <div style='margin-top: 20px;'>
                    <p style='margin: 6px 0;'>
                        Conformément à l’article 63 du code de procédure civile,<br>
                        Je me permets de vous informer que l’expertise sur <strong>{$f['objet_expertise']}</strong><br>
                        est prévue le <strong>{$f['date_expertise']}</strong> à <strong>{$f['heure']}</strong>.
                    </p>

                    <p style='margin: 20px 0 0;'>Je vous prie de bien vouloir y assister.</p>
                </div>
            </div>
        </div>
    ";
}

protected function convocationAssuranceTemplate($data)
{
    $f = $data['form_data'];

    return "
        <div style='
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
            min-height: 90vh;
            font-family: Arial, sans-serif;
            font-size: 14px;
            padding: 0 10px;
        '>
            <div style='max-width: 460px; width: 100%; line-height: 1.6;'>
                <h3 style='text-align: center; font-weight: bold; margin-bottom: 20px;'>
                    CONVOCATION À L’ASSURANCE
                </h3>

                <p style='margin: 6px 0;'><strong>MONSIEUR LE DIRECTEUR DE LA CIE D’ASSURANCE :</strong> {$f['nom_cie']}</p>
                <p style='margin: 6px 0;'><strong>DOSSIER N° :</strong> {$f['numero_dossier']}</p>
                <p style='margin: 6px 0;'><strong>JUGEMENT DU :</strong> {$f['date_jugement']}</p>
                <p style='margin: 6px 0;'><strong>AVP :</strong> {$f['date_avp']}</p>

                <div style='margin-top: 20px;'>
                    <p style='margin: 6px 0;'>
                        Conformément à l’article 63 du code de procédure civile,<br>
                        Je me permets de vous informer que l’expertise sur <strong>{$f['objet_expertise']}</strong><br>
                        est prévue le <strong>{$f['date_expertise']}</strong> à <strong>{$f['heure']}</strong>.
                    </p>

                    <p style='margin-top: 20px;'>
                        Je vous prie de bien vouloir demander à votre médecin conseil d’y assister.
                    </p>
                </div>
            </div>
        </div>
    ";
}




protected function certificatAvpInitialTemplate($data)
{
    $f = $data['form_data'];
    $today = date('d/m/Y');

    return "
        <div style='
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
            min-height: 90vh;
            font-family: Arial, sans-serif;
            font-size: 14px;
            padding: 0 10px;
        '>
            <div style='max-width: 500px; width: 100%; line-height: 1.7;'>
                <div style='text-align: right; margin-bottom: 20px;'>Casablanca, le {$today}</div>

                <div style='margin-bottom: 20px;'>
                    <strong>DR ABDELFETTAH IDRISSI KAITOUNI</strong><br>
                    EXPERT ASSERMENTÉ PRÈS LES TRIBUNAUX<br>
                    3, BLOC PO35, GH25B, OP EL FIRDAOUS<br>
                    EL OULFA CASABLANCA<br>
                    TÉL : 0522 932213
                </div>

                <h3 style='text-align: center; margin: 30px 0 20px;'>CERTIFICAT MÉDICAL INITIAL</h3>

                <p style='margin-bottom: 20px;'>
                    Je soussigné DOCTEUR ABDELFETTAH IDRISSI KAITOUNI, expert assermenté près les tribunaux,<br>
                    certifie avoir examiné ce jour <strong>{$data['patient_full_name']}</strong>,<br>
                    victime d’un AVP.
                </p>

                <p style='margin-top: 40px;'> {$f['observation']}</p>
            </div>
        </div>
    ";
}

protected function avisExpertiseArTemplate($data)
{
    $f = $data['form_data'];

    return "
        <div dir='rtl' style='
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
            min-height: 90vh;
            font-family: \"Arial\", sans-serif;
            font-size: 15px;
            padding: 0 10px;
            line-height: 1.8;
            text-align: right;
        '>
            <div style='max-width: 600px; width: 100%;'>
                <p style='font-weight: bold; text-align: center;'>المحكمة الابتدائية بالدار البيضاء</p>

                <p><strong>السيد مدير شركة التأمين :</strong> {$f['compagnie']}</p>

                <p>
                    <strong>الملف رقم :</strong> {$f['numero_dossier']}<br>
                    <strong>الحكم بتاريخ :</strong> {$f['date_jugement']}<br>
                    <strong>الخبرة بتاريخ :</strong> {$f['date_expertise']}
                </p>

                <p>عملاً بالمادة 63 من قانون المسطرة المدنية،</p>

                <p>
                    أتشرف بإخباركم بأن الخبرة المتعلقة بـ <strong>{$f['victime']}</strong><br>
                    مقررة بتاريخ <strong>{$f['date_expertise']}</strong> على الساعة 
                    <strong>{$f['heure']}</strong> و <strong>{$f['minute']}</strong> دقيقة.
                </p>

                <p>المرجو منكم التفضل بطلب حضور طبيبكم الخبير لحضور هذه الخبرة.</p>
            </div>
        </div>
    ";
}


protected function certificatAptitudeArTemplate($data)
{
    $f = $data['form_data'];
    $dob = date('d/m/Y', strtotime($data['patient_dob']));
    $today = date('d/m/Y');
    $notes = !empty($f['notes']) ? nl2br(htmlspecialchars($f['notes'])) : '...............................................................';

    return "
        <div dir='rtl' style='
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 90vh;
            font-family: \"Arial\", sans-serif;
            font-size: 15px;
            padding: 0 10px;
            line-height: 1.9;
            text-align: right;
        '>
            <div style='max-width: 600px; width: 100%;'>
                <h3 style='text-align: center; margin-bottom: 30px;'>شهادة طبية لياقة بدنية</h3>

                <p>
                    أنا الموقع أسفله، الدكتور عبد الفتاح الإدريسي القيطوني،
                    خبير محلف، أشهد بأنني قد فحصت اليوم: <strong>{$today}</strong>
                </p>

                <p><strong>{$data['patient_full_name']}</strong>  المولود(ة) بتاريخ <strong>{$dob}</strong></p>

                <p>رقم بطاقة التعريف: <strong>{$f['cin']}</strong>
                    وأُقِرّ، بناءً على الفحص السريري،
                    بأنه/أنها سليم(ة) من أي مرض معدٍ، أو مُعطِّل، أو قلبي، أو عقلي.
                </p>

                <p style='margin-top: 30px; white-space: pre-wrap;'>{$notes}</p>
            </div>
        </div>
    ";
}



protected function certificatAvpConsolidationTemplate($data)
        {
            $f = $data['form_data'];
            $today = date('d/m/Y');

            return "
                <div style='
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    height: 100%;
                    min-height: 90vh;
                    font-family: Arial, sans-serif;
                    font-size: 14px;
                    padding: 0 10px;
                '>
                    <div style='max-width: 500px; width: 100%; line-height: 1.7;'>
                        <div style='text-align: right; margin-bottom: 20px;'>Casablanca, le {$today}</div>

                        <div style='margin-bottom: 20px;'>
                            <strong>DR ABDELFETTAH IDRISSI KAITOUNI</strong><br>
                            EXPERT ASSERMENTÉ PRÈS LES TRIBUNAUX<br>
                            3, BLOC PO35, GH25B, OP EL FIRDAOUS<br>
                            EL OULFA CASABLANCA<br>
                            TÉL : 0522 932213
                        </div>

                        <h3 style='text-align: center; margin: 30px 0 20px;'>CERTIFICAT MÉDICAL DE CONSOLIDATION</h3>

                        <p style='margin-bottom: 20px;'>
                            Je soussigné DOCTEUR ABDELFETTAH IDRISSI KAITOUNI, expert assermenté près les tribunaux,<br>
                            certifie avoir examiné ce jour <strong>{$data['patient_full_name']}</strong>,<br>
                            victime d’un AVP.
                        </p>

                        <p style='margin-top: 40px;'> {$f['observation']}</p>
                    </div>
                </div>
            ";
        }


}