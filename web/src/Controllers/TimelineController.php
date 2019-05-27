<?php declare(strict_types=1);

namespace Pulse\Controllers;

use Pulse\Components\Logger;
use Pulse\Models\AccountSession\Account;
use Pulse\Models\AccountSession\AccountFactory;
use Pulse\Models\Doctor\Doctor;
use Pulse\Models\Exceptions\AccountNotExistException;
use Pulse\Models\Exceptions\AccountRejectedException;
use Pulse\Models\Exceptions\InvalidDataException;
use Pulse\Models\Exceptions\NoPrescriptionsException;
use Pulse\Models\Patient\Patient;
use Pulse\Models\Prescription\Medication;
use Pulse\Models\Prescription\Prescription;

class TimelineController extends BaseController
{
    /**
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function get()
    {
        $currentAccount = $this->getCurrentAccount();
        try {
            if ($currentAccount instanceof Doctor) {
                $accountId = $this->httpHandler()->getParameter("user");
                if ($accountId == null) {
                    $this->httpHandler()->redirect("http://$_SERVER[HTTP_HOST]/405");
                }

                $accountFactory = new AccountFactory();
                $patient = $accountFactory->getAccount($accountId, true);
                if ($patient instanceof Patient) {
                    $this->showTimelineOfPatient($patient, $currentAccount);
                    return;
                } else {
                    $this->httpHandler()->redirect("http://$_SERVER[HTTP_HOST]/405");
                }

            } else if ($currentAccount instanceof Patient) {
                $this->showTimelineOfPatient($currentAccount, $currentAccount);
                return;
            } else {
                $this->httpHandler()->redirect("http://$_SERVER[HTTP_HOST]/405");
            }
        } catch (InvalidDataException $e) {
            $this->httpHandler()->redirect("http://$_SERVER[HTTP_HOST]/500");
        } catch (AccountNotExistException $e) {
            $this->httpHandler()->redirect("http://$_SERVER[HTTP_HOST]/404");
        } catch (AccountRejectedException $e) {
            $this->httpHandler()->redirect("http://$_SERVER[HTTP_HOST]/404");
        }
    }

    /**
     * @param Patient $patient
     * @param Account $currentAccount
     * @throws InvalidDataException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    private function showTimelineOfPatient(Patient $patient, Account $currentAccount)
    {
        try {
            $parsedPrescriptions = $patient->getParsedPrescriptions();
            $this->render('PatientTimeline.htm.twig', array('prescriptions' => $parsedPrescriptions,
                'patient' => $patient->getAccountId()), $currentAccount);
        } catch (NoPrescriptionsException $e) {
            $this->render('PatientTimeline.htm.twig', array('prescriptions' => array(),
                'patient' => $patient->getAccountId()), $currentAccount);
        }
    }
}