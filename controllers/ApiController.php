<?php

namespace app\controllers;

use Yii;
use app\helpers\RazorpayHelpers;
use app\models\BookExpertVisit;
use app\models\FeasibilityReport;
use app\models\Meeting;
use app\models\Payments;
use app\models\Users;
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;
use yii\web\Response;
use PhpOffice\PhpSpreadsheet\Reader;
use PhpOffice\PhpSpreadsheet\Calculation\Exception;

class ApiController extends \yii\web\Controller
{

    private $response_code = 200;
    private $message = "Success";
    private $data;

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $headers = Yii::$app->response->headers;
        $headers->add("Cache-Control", "no-cache, no-store, must-revalidate");
        $headers->add("Pragma", "no-cache");
        $headers->add("Expires", 0);
        parent::init();
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        return [
            'corsFilter' => [
                'class' => \yii\filters\Cors::className(),
                'cors' => [
                    // restrict access to
                    'Origin' => ['http://localhost:4200', 'http://localhost:8100', 'http://3.137.151.134', 'http://dais.vesolutions.in', 'https://dais.vesolutions.in', 'http://vesolutions.in', 'https://vesolutions.in'],
                    // Allow only POST and PUT methods
                    'Access-Control-Request-Method' => ['GET', 'HEAD', 'POST', 'PUT'],
                    // Allow only headers 'X-Wsse'
                    'Access-Control-Request-Headers' => ['X-Wsse', 'Content-Type'],
                    // Allow credentials (cookies, authorization headers, etc.) to be exposed to the browser
                    'Access-Control-Allow-Credentials' => true,
                    // Allow OPTIONS caching
                    'Access-Control-Max-Age' => 3600,
                    // Allow the X-Pagination-Current-Page header to be exposed to the browser.
                    'Access-Control-Expose-Headers' => ['X-Pagination-Current-Page'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function beforeAction($action): bool
    {
        Yii::$app->controller->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    /**
     * Send JSON response
     */
    private function sendResponse(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return [
            'status' => $this->response_code,
            'message' => $this->message,
            'data' => $this->data
        ];
    }

    /**
     * Test Function
     */
    public function actionIndex(): array
    {
        return $this->sendResponse();
    }

    /**
     * Formats the user model for api response
     *
     * @param Users $model user model
     *
     * @return array
     */
    private function getUser(Users $model): array
    {
        return [
            'id' => (string)$model->user_id,
            'name' => $model->name,
            'email' => (string)$model->email,
            'phone' => (string)$model->phone,
        ];
    }

    /**
     * Register & Login user
     */
    public function actionSocialRegister(): array
    {
        $request = Yii::$app->request->bodyParams;
        if (!empty($request)) {
            $model = \app\models\Users::find()
                ->where(['email' => strtolower($request['email']), 'is_deleted' => 0, 'is_active' => 1])
                ->one();

            if (empty($model)) {
                $model = new \app\models\Users();
                $model->name = $request['name'];
                $model->email = strtolower($request['email']);
                $model->created_at = date('Y-m-d H:i:s');
                $randomString = Yii::$app->security->generateRandomString(6);
                $model->password = Yii::$app->security->generatePasswordHash($randomString);
                $model->is_email_verified = 1;
            }

            if (!empty($request['social_register_type'])) {
                $model->social_register_type = $request['social_register_type'];
            }

            $model->is_social_register = 1;
            $model->is_active = 1;

            if ($model->save()) {
                $this->response_code = 200;
                $this->message = 'User registered successfully';
                $this->data = $this->getUser($model);
            } else {
                $this->response_code = 201;
                $this->message = 'Fail to register an user';
            }
        } else {
            $this->response_code = 500;
            $this->message = 'There was an error processing the request. Please try again later.';
        }
        return $this->sendResponse();
    }

    /**
     * Book an expert visit
     */
    public function actionBookExpertVisit(): array
    {

        $request = Yii::$app->request->bodyParams;

        if (empty($request)) {
            $this->response_code = 500;
            $this->message = 'There was an error processing the request. Please try again later.';
        }

        $model = new BookExpertVisit();
        $model->user_id = $request['user_id'];
        $model->society_name = $request['society_name'];
        $model->society_type = $request['society_type'];
        $model->society_address = $request['society_address'];
        $model->contact_name = $request['contact_name'];
        $model->contact_phone = $request['contact_phone'];
        $model->contact_designation = $request['contact_designation'];
        $model->status = 1;
        $model->expert_name = 'Divyang Abhyankar';
        $model->expert_phone = '+91 79725 92726';

        $book_date = date('Y:m:d H:i:s');
        $visit_date = date('Y-m-d', strtotime($book_date . ' +1 day'));

        $model->visit_date = $visit_date;
        $model->created_at = $book_date;

        if ($model->save()) {
            $booked_visits = BookExpertVisit::find()
                ->where(['user_id' => $model->user_id])
                ->orderBy(['created_at' => SORT_DESC])
                ->all();
            $this->response_code = 200;
            $this->message = 'Visit Booked';
            $this->data = $booked_visits;
        }

        return $this->sendResponse();

    }

    /**
     * Get list of booked visits
     */
    public function actionExpertVisitHistory($user_id): array
    {

        $model = BookExpertVisit::find()
            ->where(['user_id' => $user_id])
            ->orderBy(['created_at' => SORT_DESC])
            ->all();

        $this->response_code = 200;
        $this->data = $model;

        return $this->sendResponse();
    }

    /**
     * Book a meeting with DAIS
     */
    public function actionBookMeeting(): array
    {

        $request = Yii::$app->request->bodyParams;

        $meeting = new Meeting();
        $meeting->user_id = $request['user_id'];
        $meeting->society_name = $request['society_name'];
        $meeting->contact_name = $request['contact_name'];
        $meeting->contact_phone = $request['contact_phone'];
        $meeting->contact_designation = $request['contact_designation'];
        $meeting->status = 0;
        $meeting->visit_date = $request['visit_date'];
        $meeting->created_at = date('Y-m-d H:i:s');
        $meeting->amount = 25000;

        if ($meeting->save()) {

            $model = Meeting::find()
                ->where(['user_id' => $request['user_id']])
                ->all();
            $this->data = $model;
            $this->message = 'Meeting Booked';

            return $this->sendResponse();
        }

        return $this->sendResponse();
    }

    /**
     * Get booked meetings
     */
    public function actionShowBookedMeeting($user_id): array
    {

        $model = Meeting::find()
            ->where(['user_id' => $user_id])
            ->orderBy(['meeting_id' => SORT_DESC])
            ->all();

        $this->response_code = 200;
        $this->data = $model;

        return $this->sendResponse();
    }

    /**
     * Save inputs of Feasibility report (FREE) (ANGULAR - CREATE)
     */
    public function actionFreeFeasibilityReport(): array
    {

        $request = Yii::$app->request->bodyParams;

        $feasibilityReport = [
            'FeasibilityReport' => $request
        ];

        $model = new FeasibilityReport();
        $model->load($feasibilityReport);
        $model->created_at = date('Y-m-d H:i:s');

        if ($model->save()) {

            $reports = FeasibilityReport::find()
                ->where(['user_id' => $model->user_id, 'is_paid' => 0])
                ->orderBy(['feasibility_report_id' => SORT_DESC])
                ->all();

            $this->message = "Feasibility report generated successfully";
            $this->response_code = 200;
            $this->data = $reports;
        } else {
            $this->message = "Internal service error";
            $this->response_code = 500;
            $this->data = $model->getErrors();
        }

        return $this->sendResponse();
    }

    /**
     * Get list of feasibility reports (FREE) (ANGULAR - LIST)
     */
    public function actionFreeFeasibilityReportHistory($user_id): array
    {

        $reports = FeasibilityReport::find()
            ->where(['user_id' => $user_id, 'is_paid' => 0])
            ->orderBy(['feasibility_report_id' => SORT_DESC])
            ->all();

        $this->response_code = 200;
        $this->data = $reports;

        return $this->sendResponse();
    }

    /**
     * Get Feasibility report (FREE & PAYED) (ANGULAR - VIEW REPORT DETAILS)
     */
    public function actionFreeFeasibilityReportDetails($user_id, $feasibility_report_id): array
    {

        $report = FeasibilityReport::find()
            ->where(['user_id' => $user_id, 'feasibility_report_id' => $feasibility_report_id])
            ->asArray()
            ->one();

        if (empty($report)) {

            $this->response_code = 404;
            $this->message = "report not found";
            $this->data = $report;

            return $this->sendResponse();
        }

        /**
         * PART 1 STARTS
         * To Determine what is maximum carpet area available in the project.
         * */

        $part_1['plot_area'] = $report['plot_size'];
        $part_1['road_width'] = 13.40;

        $deduction_for['road_set_back_area'] = 0;
        $deduction_for['proposed_dp_road'] = 0;
        $deduction_for['any_reservation'] = 0;
        $deduction_for['total'] = $deduction_for['road_set_back_area'] + $deduction_for['proposed_dp_road'] + $deduction_for['any_reservation'];

        $part_1['deduction_for'] = $deduction_for;
        $part_1['balance_area_of_plot'] = $part_1['plot_area'] - $deduction_for['total'];
        $part_1['net_area_of_plot'] = $part_1['balance_area_of_plot'];

        /* FSI PERMISSIBLE  as per Reg.No.30, Table 12 of DCPR 30. */
        $fsi_permissible['zonal_basic_fsi'] = 1;
        $fsi_permissible['zonal_basic_fsi_area'] = $part_1['net_area_of_plot'] * $fsi_permissible['zonal_basic_fsi'];
        $fsi_permissible['additional_fsi'] = 0.50;
        $fsi_permissible['additional_fsi_area'] = $part_1['net_area_of_plot'] * $fsi_permissible['additional_fsi'];
        $fsi_permissible['admissible_tdr'] = 0.70; // TODO: Need to update according to road width
        $fsi_permissible['admissible_tdr_area'] = $part_1['net_area_of_plot'] * $fsi_permissible['admissible_tdr'];

        $fsi_permissible['maximum_fsi_cap'] = $fsi_permissible['zonal_basic_fsi'] + $fsi_permissible['additional_fsi'] + $fsi_permissible['admissible_tdr'];
        $fsi_permissible['total'] = $fsi_permissible['zonal_basic_fsi_area'] + $fsi_permissible['additional_fsi_area'] + $fsi_permissible['admissible_tdr_area'];

        $part_1['fsi_permissible'] = $fsi_permissible;

        /* FSI PERMISSIBLE AS PER REG.33(7)(B) */
        $fsi_permissible_2['exist_authorized_b_u_area'] = $report['existing_built_up_area']; // TODO: INPUT
        $fsi_permissible_2['incentive_add_b_u_area'] = 6996.60;
        $fsi_permissible_2['total'] = $fsi_permissible_2['exist_authorized_b_u_area'] + $fsi_permissible_2['incentive_add_b_u_area'];
        $part_1['fsi_permissible_2'] = $fsi_permissible_2;

        /* BALANCE B/U AREA THAT MAY AVAIL BY FSI BY CHARGING PREMIUM/PURCHASING TDR */
        $part_1['balance_b_u_area'] = $fsi_permissible['total'] - $fsi_permissible_2['total'];

        /* PERM. B/U AREA AS PER REG 33(7)(B) */
        $part_1['perm_b_u_area'] = $part_1['balance_b_u_area'] + $fsi_permissible_2['total'];

        /* FUNGIBLE FSI As per Reg.No.31(3) of DCPR 2034 (FUNGIBLE FSI 35% ) */
        $part_1['fungible_fsi'] = $part_1['perm_b_u_area'] / 100 * 35;

        /* TOTAL PERMISSIBLE BUILT UP AREA including Fungible */
        $part_1['total_permissible_built_up_area_including_fungible'] = $part_1['perm_b_u_area'] + $part_1['fungible_fsi'];

        /* RERA CARPET AREA (92%) */
        $part_1['rera_carpet_area'] = $part_1['total_permissible_built_up_area_including_fungible'] / 100 * 92;

        $note['fsi_by_charging_premium'] = $fsi_permissible['additional_fsi_area'];
        $note['tdr_to_be_purchased_from_open_market'] = $part_1['balance_b_u_area'] - $note['fsi_by_charging_premium'];
        $note['existing_built_up_area_as_per_society'] = $fsi_permissible_2['exist_authorized_b_u_area'];

        /* FREE FUNGIBLE 35% OF EXISTING B/U AREA */
        $note['free_fungible'] = $note['existing_built_up_area_as_per_society'] / 100 * 35;
        /* FUNGIBLE BY CHARGING PREMIUM = Total permissible fungible (8) LESS Free fungible  */
        $note['fungible_by_charging_premium'] = $part_1['fungible_fsi'] + $part_1['perm_b_u_area'];
        $note['fungible_by_charging_premium'] = $part_1['fungible_fsi'] - $note['free_fungible'];

        /* Existing members (Residential)  */
        $note['existing_members_residential'] = $report['no_of_tenants'];

        /* Existing members (Commercial)  */
        $note['existing_members_commercial'] = 0;

        /* AS PER DCPR 33(7)(B) Additional FSI  = 15% OF Existing Builtup area OR 10.00Sq.M. per existing member; whichever is greater */
        $note['additional_fsi_as_dcpr'] = $note['existing_members_residential'] * 10;

        /* EXISTING CARPET AREA STATEMENT AS PER SOCIETY DOC'S */

        $note['total_existing_carpet_area'] = $report['area_currently_consumed'];

        /* CONSTRUCTION AREA  */

        /* CONSTRUCTION OF NET BUILT-UP AREA */
        $note['construction_of_net_built_up_area'] = $part_1['total_permissible_built_up_area_including_fungible'];

        /* CONSTRUCTION AREA OF STAIRCASE AND LIFT (25%) */
        $note['construction_area_of_staircase_and_lift'] = $note['construction_of_net_built_up_area'] / 100 * 25;

        /* CONSTRUCTION AREA FOR PARKING (25%) */
        $note['construction_area_for_parking'] = $note['construction_of_net_built_up_area'] / 100 * 25;

        /* TOTAL CONSTRUCTION AREA */
        $note['total_construction_area'] = $note['construction_of_net_built_up_area'] + $note['construction_area_of_staircase_and_lift'] + $note['construction_area_for_parking'];

        $part_1['note'] = $note;

        /* ADDITIONAL AREA */
        $additional_area['additional_area'] = $part_1['total_permissible_built_up_area_including_fungible'] - $part_1['net_area_of_plot'];
        $additional_area['slum'] = $note['tdr_to_be_purchased_from_open_market'];
        $additional_area['gen_tdr_and_incentive'] = $fsi_permissible['admissible_tdr_area'] - $additional_area['slum'];
        $additional_area['FSI_0_50'] = $note['fsi_by_charging_premium'];
        $additional_area['fungible'] = $part_1['fungible_fsi'];
        $additional_area['total'] = $additional_area['slum'] + $additional_area['gen_tdr_and_incentive'] + $additional_area['FSI_0_50'] + $additional_area['fungible'];

        $additional_area['additional_area_sqm'] = $additional_area['additional_area'] / 10.764;

        /* PERCENTAGE OF ADDITIONAL FSI */
        $additional_area['slum_percentage'] = 100 * $additional_area['slum'] / $additional_area['additional_area'];
        $additional_area['gen_tdr_and_incentive_percentage'] = 100 * $additional_area['gen_tdr_and_incentive'] / $additional_area['additional_area'];
        $additional_area['FSI_0_50_percentage'] = 100 * $additional_area['FSI_0_50'] / $additional_area['additional_area'];
        $additional_area['fungible_percentage'] = 100 * $additional_area['fungible'] / $additional_area['additional_area'];
        $additional_area['total_percentage'] = $additional_area['slum_percentage'] + $additional_area['gen_tdr_and_incentive_percentage'] + $additional_area['FSI_0_50_percentage'] + $additional_area['fungible_percentage'];


        $part_1['additional_area'] = $additional_area;

        /* DEFICIENT AREA */
        /* Note:- At present 25% area of net built up area is considered as Deficient area */
        $deficient_area['deficient_area'] = $part_1['total_permissible_built_up_area_including_fungible'] / 100 * 25;
        $deficient_area['deficient_area_sqm'] = $deficient_area['deficient_area'] / 10.764;
        /* RATE */
        $deficient_area['rr_rate'] = $report['residential_redirecionar_rate']; //TODO: Calculate
        /* R.R.RATE x 25% */
        $deficient_area['rr_rate_25'] = $deficient_area['rr_rate'] / 4;

        /* Deficient area as per percentage */
        $deficient_area['slum_percentage'] = $deficient_area['deficient_area_sqm'] / 100 * $additional_area['slum_percentage'];
        $deficient_area['gen_tdr_and_incentive_percentage'] = $deficient_area['deficient_area_sqm'] / 100 * $additional_area['gen_tdr_and_incentive_percentage'];
        $deficient_area['FSI_0_50_percentage'] = $deficient_area['deficient_area_sqm'] / 100 * $additional_area['FSI_0_50_percentage'];
        $deficient_area['fungible_percentage'] = $deficient_area['deficient_area_sqm'] / 100 * $additional_area['fungible_percentage'];
        $deficient_area['total_percentage'] = $deficient_area['slum_percentage'] + $deficient_area['gen_tdr_and_incentive_percentage'] + $deficient_area['FSI_0_50_percentage'] + $deficient_area['fungible_percentage'];

        /* Deficient AMOUNT */
        $deficient_area['amount']['slum_amount'] = $deficient_area['rr_rate_25'] * $deficient_area['slum_percentage'];
        $deficient_area['amount']['gen_tdr_and_incentive_amount'] = $deficient_area['rr_rate_25'] * $deficient_area['gen_tdr_and_incentive_percentage'];
        $deficient_area['amount']['FSI_0_50_amount'] = $deficient_area['rr_rate_25'] * $deficient_area['FSI_0_50_percentage'];
        $deficient_area['amount']['fungible_amount'] = $deficient_area['rr_rate_25'] * $deficient_area['fungible_percentage'];

        /* Deficient AMOUNT AS PER PERCENTAGE */
        $deficient_area['amount_as_per_percentage']['slum'] = $deficient_area['amount']['slum_amount'] / 100 * 10;
        $deficient_area['amount_as_per_percentage']['gen_tdr_and_incentive'] = $deficient_area['amount']['gen_tdr_and_incentive_amount'] / 100 * 100;
        $deficient_area['amount_as_per_percentage']['FSI_0_50'] = $deficient_area['amount']['FSI_0_50_amount'] / 100 * 100;
        $deficient_area['amount_as_per_percentage']['fungible'] = $deficient_area['amount']['fungible_amount'] / 100 * 25;
        $deficient_area['amount_as_per_percentage']['total'] =
            $deficient_area['amount_as_per_percentage']['slum']
            + $deficient_area['amount_as_per_percentage']['gen_tdr_and_incentive']
            + $deficient_area['amount_as_per_percentage']['FSI_0_50']
            + $deficient_area['amount_as_per_percentage']['fungible'];

        /* Deficient Premium as per Telescopic method by adding 20% */
        $deficient_area['deficient_premium'] = $deficient_area['amount_as_per_percentage']['total'] * 1.2;

        $part_1['deficient_area'] = $deficient_area;

        $report['part_1'] = $part_1;

        /**
         * PART 1 ENDS
         */

        /**
         * PART 2 BEGINS
         */
        $part_2 = [];
        //section 1

        $plot_cost = [];
        $rr_rate = $report['residential_redirecionar_rate'];
        $fungible_area_in_sq_feet = $part_1['total_permissible_built_up_area_including_fungible'];
        $fungible_area_in_sq_m = $part_1['total_permissible_built_up_area_including_fungible'] / 10.764;
        $plot_cost['luc_tax']['area'] = $fungible_area_in_sq_feet;
        $plot_cost['luc_tax']['rate'] = $report['residential_redirecionar_rate'] * 1.6;
        $plot_rate_percent = $plot_cost['luc_tax']['rate'] / 100;
        $plot_cost['luc_tax']['amount'] = (($fungible_area_in_sq_m * $plot_rate_percent) / 10000000) * 2;
        $plot_cost ['debris_management_noc']['amount'] = 0.15;
        $plot_cost ['processing_fee_for_project_loan']['amount'] = 0.07;
        $plot_cost ['purchasing_shares_from_bank']['amount'] = $plot_cost ['processing_fee_for_project_loan']['amount'] * 2.5;
        $plot_cost['stamp_duty_registration_charges'] = 0;
        $plot_cost['total_amount'] = $plot_cost['luc_tax']['amount'] + $plot_cost ['debris_management_noc']['amount'] + $plot_cost ['processing_fee_for_project_loan']['amount'] + $plot_cost ['purchasing_shares_from_bank']['amount'];
//        debugPrint($plot_cost);
//        exit;
        $part_2 ['plot_cost'] = $plot_cost;
        /**
         * Section 1 ends here
         * */

        /**
         * Section 2
         * */

        $cost_of_approval = [];
        $total_construction_area_sq_feet = $part_1['note']['total_construction_area']; //sq feet
        $total_construction_area_sq_metre = $part_1['note']['total_construction_area'] / 10.764; //sq feet
        $cost_of_approval['scrutiny_fees']['area'] = $total_construction_area_sq_metre;
        $cost_of_approval['scrutiny_fees']['rate'] = 86;
        $cost_of_approval['scrutiny_fees']['amount'] = ($total_construction_area_sq_metre * 86) / 10000000;

        $cost_of_approval['cfo_scrutiny_fees']['area'] = $total_construction_area_sq_metre;
        $cost_of_approval['cfo_scrutiny_fees']['rate'] = 53;
        $cost_of_approval['cfo_scrutiny_fees']['amount'] = ($total_construction_area_sq_metre * 53) / 10000000;

        $tdr_to_be_purchased_from_open_market_sq_mt = $part_1['note']['tdr_to_be_purchased_from_open_market'] / 10.764;
        $cost_of_approval['tdr_utilization']['area'] = $tdr_to_be_purchased_from_open_market_sq_mt;
        $cost_of_approval['tdr_utilization']['rate'] = (30250 / 100) * 5;
        $cost_of_approval['tdr_utilization']['amount'] = ($tdr_to_be_purchased_from_open_market_sq_mt * $cost_of_approval['tdr_utilization']['rate']) / 10000000;

        $cost_of_tdr['slum_tdr']['base_amount'] = $tdr_to_be_purchased_from_open_market_sq_mt;
        $cost_of_tdr['slum_tdr']['percent'] = 70;
        $cost_of_tdr['slum_tdr']['amount'] = ($rr_rate / 100) * 70;
        $cost_of_tdr['slum_tdr']['true_amount'] = ($cost_of_tdr['slum_tdr']['base_amount'] * $cost_of_tdr['slum_tdr']['amount']) / 10000000;
        $cost_of_tdr['gen_tdr']['percent'] = 35;
        $cost_of_approval['cost_of_tdr'] = $cost_of_tdr;

        $cost_of_approval['fsi_by_charging_premium']['area'] = $part_1['note']['fsi_by_charging_premium'] / 10.764;
        $cost_of_approval['fsi_by_charging_premium']['rate'] = 20055.00;
        $cost_of_approval['fsi_by_charging_premium']['amount'] = ((($cost_of_approval['fsi_by_charging_premium']['area'] * 20055.00) / 10000000) / 100) * 50;


        $cost_of_approval['cost_of_fungible_fsi_premium']['area'] = $part_1['note']['fungible_by_charging_premium'] / 10.764;
        $cost_of_approval['cost_of_fungible_fsi_premium']['rate'] = 20055.00;
        $cost_of_approval['cost_of_fungible_fsi_premium']['amount'] = ((($cost_of_approval['cost_of_fungible_fsi_premium']['area'] * $cost_of_approval['cost_of_fungible_fsi_premium']['rate']) / 10000000) / 100) * 50;

        $cost_of_approval['stair_case_lift_area']['area'] = $part_1['note']['construction_area_of_staircase_and_lift'] / 10.764;
        $cost_of_approval['stair_case_lift_area']['rate'] = ($rr_rate / 100) * 25;
        $cost_of_approval['stair_case_lift_area']['amount'] = ((($cost_of_approval['stair_case_lift_area']['area'] * $cost_of_approval['stair_case_lift_area']['rate']) / 10000000) / 100) * 50;

        $cost_of_approval['development_charges_built_up_charges']['area'] = $part_1['note']['construction_of_net_built_up_area'] / 10.764;
        $cost_of_approval['development_charges_built_up_charges']['rate'] = ($rr_rate / 100) * 4;
        $cost_of_approval['development_charges_built_up_charges']['amount'] = ($cost_of_approval['development_charges_built_up_charges']['area'] * $cost_of_approval['development_charges_built_up_charges']['rate']) / 10000000;

        $cost_of_approval['development_charges_plot_component']['area'] = $part_1['balance_area_of_plot'] / 10.764;
        $cost_of_approval['development_charges_plot_component']['rate'] = ($rr_rate / 100) * 1;
        $cost_of_approval['development_charges_plot_component']['amount'] = ($cost_of_approval['development_charges_plot_component']['area'] * $cost_of_approval['development_charges_plot_component']['rate']) / 10000000;

        $cost_of_approval['development_cess']['area'] = 0;
        $cost_of_approval['development_cess']['rate'] = 0;
        $cost_of_approval['development_cess']['amount'] = 0;

        $cost_of_approval['labour_cess']['area'] = $part_1['note']['construction_of_net_built_up_area'] / 10.764;
        $cost_of_approval['labour_cess']['rate'] = (30250 / 100) * 1;
        $cost_of_approval['labour_cess']['amount'] = ($cost_of_approval['labour_cess']['area'] * $cost_of_approval['labour_cess']['rate']) / 10000000;

        $cost_of_approval['deficiency_premium_approximate']['area'] = "";
        $cost_of_approval['deficiency_premium_approximate']['rate'] = "";
        $cost_of_approval['deficiency_premium_approximate']['amount'] = (($part_1['deficient_area']['deficient_premium'] / 10000000) / 100) * 50;

        $cost_of_approval['extra_water_charge']['area'] = $part_1['note']['construction_of_net_built_up_area'] / 10.764;
        $cost_of_approval['extra_water_charge']['rate'] = 300;
        $cost_of_approval['extra_water_charge']['amount'] = ($cost_of_approval['extra_water_charge']['area'] * $cost_of_approval['extra_water_charge']['rate']) / 10000000;

        $cost_of_approval['extra_sewage_charge']['area'] = $part_1['note']['construction_of_net_built_up_area'] / 10.764;
        $cost_of_approval['extra_sewage_charge']['rate'] = 285;
        $cost_of_approval['extra_sewage_charge']['amount'] = ($cost_of_approval['extra_sewage_charge']['area'] * $cost_of_approval['extra_sewage_charge']['rate']) / 10000000;

        $cost_of_approval['bmc_approval_cost']['area'] = $part_1['note']['construction_of_net_built_up_area'];
        $cost_of_approval['bmc_approval_cost']['rate'] = 175; //TODO
        $cost_of_approval['bmc_approval_cost']['amount'] = ($cost_of_approval['bmc_approval_cost']['area'] * $cost_of_approval['bmc_approval_cost']['rate']) / 10000000;

        $cost_of_approval['total_cost_approval_tdr_premium'] = $cost_of_approval['scrutiny_fees']['amount'] +
            $cost_of_approval['cfo_scrutiny_fees']['amount'] +
            $cost_of_approval['tdr_utilization']['amount'] +
            $cost_of_tdr['slum_tdr']['true_amount'] +
            $cost_of_approval['fsi_by_charging_premium']['amount'] +
            $cost_of_approval['cost_of_fungible_fsi_premium']['amount'] +
            $cost_of_approval['stair_case_lift_area']['amount'] +
            $cost_of_approval['development_charges_built_up_charges']['amount'] +
            $cost_of_approval['development_charges_plot_component']['amount'] +
            $cost_of_approval['development_cess']['amount'] +
            $cost_of_approval['labour_cess']['amount'] +
            $cost_of_approval['deficiency_premium_approximate']['amount'] +
            $cost_of_approval['extra_water_charge']['amount'] +
            $cost_of_approval['extra_sewage_charge']['amount'] +
            $cost_of_approval['bmc_approval_cost']['amount'];

        $part_2 ['cost_of_approval'] = $cost_of_approval;
//        debugPrint($cost_of_approval);
//        exit;
        /**
         * Section 3
         * */
        $constructionCost = [];
        $constructionCost['const_area']['area'] = $part_1['note']['total_construction_area'];
        $constructionCost['const_area']['rate'] = 2600.00;
        $constructionCost['const_area']['amount'] = ($constructionCost['const_area']['area'] * $constructionCost['const_area']['rate']) / 10000000;

        $constructionCost['puzzle_parking']['amount'] = 2; //TODO

        $constructionCost['construction_cost_parking_total'] = $constructionCost['const_area']['amount'] + $constructionCost['puzzle_parking']['amount'];
        $constructionCost['construction_cost_parking_gst'] = ($constructionCost['construction_cost_parking_total'] / 100) * 18;
        $constructionCost['total_construction_expense'] = $constructionCost['construction_cost_parking_total'] + $constructionCost['construction_cost_parking_gst'];
        $part_2['constructionCost'] = $constructionCost;
        $rental_shifting_charges = [];
        $rental_shifting_charges['one_month_rent']['cost'] = (float)$part_1['note']['total_existing_carpet_area'];
        $rental_shifting_charges['one_month_rent']['unit'] = 50; //TODO
        $rental_shifting_charges['one_month_rent']['amount'] = ($rental_shifting_charges['one_month_rent']['cost'] * $rental_shifting_charges['one_month_rent']['unit']) / 10000000;

        $rental_shifting_charges['one_year_rent']['cost'] = (float)$rental_shifting_charges['one_month_rent']['amount'];
        $rental_shifting_charges['one_year_rent']['unit'] = 12;
        $rental_shifting_charges['one_year_rent']['amount'] = $rental_shifting_charges['one_month_rent']['amount'] * 12;

        $rental_shifting_charges['second_year_rent']['amount'] = $rental_shifting_charges['one_year_rent']['amount'];
        $rental_shifting_charges['total_rent'] = $rental_shifting_charges['one_year_rent']['amount'] + $rental_shifting_charges['second_year_rent']['amount'];

        $holder = number_format($rental_shifting_charges['total_rent'], 2);
        $holder2 = $rental_shifting_charges['total_rent'];
//        debugPrint($holder);
//        debugPrint($holder2);
//        exit;

        $brokerage = [];
        $brokerage['area'] = $rental_shifting_charges['one_month_rent']['cost'];
        $brokerage['rate'] = $rental_shifting_charges['one_month_rent']['unit'];
        $brokerage['amount'] = $rental_shifting_charges['one_month_rent']['amount'];
        $rental_shifting_charges['brokerage'] = $brokerage;

        $shifting_and_reshifting = [];
        $shifting_and_reshifting['existing_members'] = $part_1['note']['existing_members_residential'];
        $shifting_and_reshifting['unit'] = 30000;
        $shifting_and_reshifting['amount'] = ($shifting_and_reshifting['existing_members'] * $shifting_and_reshifting['unit']) / 10000000;
        $rental_shifting_charges['shifting_and_reshifting'] = $shifting_and_reshifting;


        $shifting_and_reshifting['total_cost_of_rental_shifting_brokerage'] = $rental_shifting_charges['total_rent'] + $brokerage['amount'] + $shifting_and_reshifting['amount'];

        $rental_shifting_charges['shifting_and_reshifting'] = $shifting_and_reshifting;
        $part_2['rental_shifting_charges'] = $rental_shifting_charges;

        $all_consultation_fee_including_gst = [];
        $all_consultation_fee_including_gst ['amount'] = (($constructionCost['construction_cost_parking_total'] / 100) * 10) * 1.18;

        $part_2 ['all_consultation_fee_including_gst'] = $all_consultation_fee_including_gst;
        $corpus_fund = [];
        $corpus_fund ['area'] = "";
        $corpus_fund ['rate'] = "";
        $corpus_fund ['amount'] = "";
        $part_2['corpus_fund'] = $corpus_fund;
        /**
         * Part 3 early Calculation (All Calculations area SQ. FT)
         * */
        $part_3 = [];
        $part_3['total_rera_carpet'] = $part_1['rera_carpet_area'];
        $part_3['existing_carpet_area'] = 24111.00; //TODO
        $part_3['percentage_of_additional_carpet_area'] = 16; //TODO
        $part_3['percentage_of_additional_carpet_area'] = ($part_3['existing_carpet_area'] / 100) * $part_3['percentage_of_additional_carpet_area'];
        $part_3['rera_carpet_area_for_existing_members'] = $part_3['existing_carpet_area'] + $part_3['percentage_of_additional_carpet_area'];
        $part_3['rera_carpet_area_for_sale'] = $part_3['total_rera_carpet'] - $part_3['rera_carpet_area_for_existing_members'];
        $part_3['break_even_for_carpet_area_sale'] = ""; //Not calculated yet

        $part_3['sale_rate_considered'] = 18500.00; //TODO
        $part_3['sale_recovery'] = ($part_3['sale_rate_considered'] * $part_3['rera_carpet_area_for_sale']) / 10000000; //TODO

        $part_2['brokerage_and_commision_sale_value']['amount'] = $part_3['sale_recovery'];
        $part_2['brokerage_and_commision_sale_value']['total'] = ($part_3['sale_recovery'] / 100) * 2.5;

        $part_2['total_one_to_seven'] = $part_2['plot_cost']['total_amount'] + $part_2['cost_of_approval']['total_cost_approval_tdr_premium'] + $part_2['constructionCost']['total_construction_expense']
            + $part_2['rental_shifting_charges']['shifting_and_reshifting']['total_cost_of_rental_shifting_brokerage'] + $part_2['all_consultation_fee_including_gst']['amount'] + $part_2['brokerage_and_commision_sale_value']['total'];

        $part_2['total_one_to_seven_contingences'] = ($part_2['total_one_to_seven'] / 100) * 3;
        $part_2['total_cost_part_2'] = $part_2['total_one_to_seven'] + $part_2['total_one_to_seven_contingences'];

        $part_2['expected_investment'] = ($part_2['total_cost_part_2'] / 100) * 40;
        $part_2['interest_of_investment_per_annum'] = ($part_2['expected_investment'] / 100) * 12.5;
        $part_2['interest_of_two_years'] = $part_2['interest_of_investment_per_annum'] * 2;

        $part_2['part_two_total_cost_of_project'] = $part_2['total_cost_part_2'] + $part_2['interest_of_two_years'];

        $part_3['total_project_cost'] = $part_2['part_two_total_cost_of_project'] * 10000000;
        $part_3['break_even_for_carpet_area_sale'] = $part_3['total_project_cost'] / $part_3['rera_carpet_area_for_sale'];
        $part_3['by_returning_share_to_bank'] = $part_2['plot_cost']['purchasing_shares_from_bank']['amount'];
        $part_3['from_parking'] = 3.00; //TODO
        $part_3['total_recovery'] = $part_3['sale_recovery'] + $part_3['by_returning_share_to_bank'] + $part_3['from_parking'];
        $part_3['balance'] = $part_3['total_recovery'] - $part_2['part_two_total_cost_of_project'];
        $part_3['stamp_duty_and_charges'] = ($part_3['sale_recovery'] / 100) * 5;
        $part_3['net_balance'] = $part_3['balance'] - $part_3['stamp_duty_and_charges'];

//        debugPrint($part_3);
//        debugPrint($part_2);
//        exit;

//        debugPrint($rental_shifting_charges);
//        exit;
        $report['part_2'] = $part_2;
        $report['part_3'] = $part_3;

        $this->response_code = 200;
        $this->data = $report;

        return $this->sendResponse();
    }

    /**
     * Save inputs of Feasibility report, Save Payment info and return razorpay payment details
     */
    public function actionFeasibilityReport(): array
    {

        $request = Yii::$app->request->bodyParams;

        $feasibilityReport = [
            'FeasibilityReport' => $request
        ];

        $model = new FeasibilityReport();
        $model->load($feasibilityReport);
        $model->is_paid = FeasibilityReport::PAYMENT_INIT;
        $model->is_payment_processed = 1;
        $model->created_at = date('Y-m-d H:i:s');

        if ($model->save()) {

            $reports = FeasibilityReport::find()
                ->where([
                    'user_id' => $model->user_id,
                    'is_paid' => 1,
                    'is_payment_processed' => FeasibilityReport::PAYMENT_SUCCESS
                ])
                ->orderBy(['feasibility_report_id' => SORT_DESC])
                ->all();

            $payment = new Payments();
            $payment->payment_type = Payments::FEASIBILITY_REPORT;
            $payment->payment_type_id = $model->feasibility_report_id;
            $payment->total_amount = Yii::$app->params['FEASIBILITY_REPORT_COST'];
            $payment->payment_date = date('Y-m-d H:i:s');
            $payment->is_processed = Payments::PAYMENT_INIT;
            $payment->currency_code = Yii::$app->params['displayCurrency'];
            $payment->save();

            $paymentDetails = RazorpayHelpers::pay(
                $payment->payment_id,
                $payment->total_amount,
                'Feasibility Report',
                'Devtaa A.I Services',
                $model->user->name,
                $model->user->email,
                $model->user->phone,
                $payment->currency_code
            );

            $this->message = "Feasibility report generated successfully";
            $this->response_code = 200;
            $this->data = [
                'reports' => $reports,
                'report' => $model,
                'paymentDetails' => $paymentDetails
            ];
        } else {
            $this->message = "Internal service error";
            $this->response_code = 500;
            $this->data = $model->getErrors();
        }

        return $this->sendResponse();

    }

    /**
     * Get list of purchased Feasibility report by the user
     */
    public function actionFeasibilityReportHistory($user_id): array
    {

        $reports = FeasibilityReport::find()
            ->where([
                'user_id' => $user_id,
                'is_paid' => 1,
                'is_payment_processed' => FeasibilityReport::PAYMENT_SUCCESS
            ])
            ->orderBy(['feasibility_report_id' => SORT_DESC])
            ->all();

        $this->response_code = 200;
        $this->data = $reports;

        return $this->sendResponse();
    }

    /**
     * Verify user payment for Razorpay and save payment details
     */
    public function actionVerifyRazorpay(): array
    {

        $keyId = \Yii::$app->params['keyId'];
        $keySecret = \Yii::$app->params['keySecret'];

        if (empty(Yii::$app->request->post('razorpay_payment_id')) === false) {

            $api = new Api($keyId, $keySecret);

            try {
                $attributes = [
                    'razorpay_order_id' => Yii::$app->request->post('razorpay_order_id'),
                    'razorpay_payment_id' => Yii::$app->request->post('razorpay_payment_id'),
                    'razorpay_signature' => Yii::$app->request->post('razorpay_signature'),
                ];
                $api->utility->verifyPaymentSignature($attributes);
            } catch (SignatureVerificationError $e) {
                $this->response_code = 200;
                $this->message = $e->getMessage();
                return $this->sendResponse();
            }

            // Update Payment Model
            $paymentModel = Payments::findOne(['razorpay_order_id' => $attributes['razorpay_order_id']]);

            if (empty($paymentModel)) {
                $this->response_code = 501;
                $this->message = 'Payment Failed, Please try again later';
            }

            $paymentModel->is_processed = Payments::PAYMENT_SUCCESS;
            $paymentModel->razorpay_payment_id = $attributes['razorpay_payment_id'];
            $paymentModel->razorpay_signature = $attributes['razorpay_signature'];

            if ($paymentModel->save(false)) {

                /**
                 * IF Payment is done for Feasibility Report
                 * */
                if ($paymentModel->payment_type === Payments::FEASIBILITY_REPORT) {

                    $reportModel = FeasibilityReport::findOne($paymentModel->payment_type_id);

                    if (empty($reportModel)) {
                        $this->response_code = 502;
                        $this->message = 'Payment Failed, Please try again later';
                    }

                    $reportModel->is_paid = 1;
                    $reportModel->is_payment_processed = FeasibilityReport::PAYMENT_SUCCESS;

                    if ($reportModel->save(false)) {
                        $this->response_code = 200;
                        $this->message = 'Payment Successful';
                        return $this->sendResponse();
                    } else {
                        $this->response_code = 503;
                        $this->message = 'Payment Failed, Please try again later';
                    }
                }

                $this->response_code = 201;
                $this->message = 'Payment Successful, Unknown payment type';
                return $this->sendResponse();
            } else {
                $this->response_code = 504;
                $this->message = 'Payment Failed, Please try again later';
            }
        } else {
            $this->response_code = 500;
            $this->message = 'Payment Failed, Please try again later';
        }

        return $this->sendResponse();
    }

    /**
     * Demo API
     * @throws Exception
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function actionDemo(): array
    {
        $request = Yii::$app->request->bodyParams;

        if (empty($request)) {
            $this->response_code = 500;
            $this->message = 'There was an error processing the request. Please try again later.';
        }

        $number1 = $request['num1'];
        $number2 = $request['num2'];

        $inputFileName =  getcwd() . '/spreadsheet.xlsx';
        $reader = new Reader\Xlsx();
        $spreadsheet = $reader->load($inputFileName);

        $spreadsheet->getActiveSheet()->getCell('B1')->setValue($number1);
        $spreadsheet->getActiveSheet()->getCell('B2')->setValue($number2);

        $this->data = [
            'demo' => 'Hello World',
            'b1' => $spreadsheet->getActiveSheet()->getCell('B1')->getValue(),
            'b2' => $spreadsheet->getActiveSheet()->getCell('B2')->getValue(),
            'b3' => $spreadsheet->getActiveSheet()->getCell('B3')->getCalculatedValue()
        ];
        return $this->sendResponse();
    }

    /**
     * Generate Report API
     */
    public function actionGenerateReport(): array
    {
        $request = Yii::$app->request->bodyParams;

        if (empty($request)) {
            $this->response_code = 500;
            $this->message = 'There was an error processing the request. Please try again later.';
        }

        try {
            $inputFileName =  'FEASIBILITY';
            $reader = new Reader\Xlsx();
            $spreadsheet = $reader->load(getcwd()."/$inputFileName.xlsx");

            $sheets = $spreadsheet->getAllSheets();

            $sheet1 = $sheets[0];
            $sheet1->getCell('G3')->setValue($request['plot_area']);
            $sheet1->getCell('E15')->setValue($request['admissible_tdr']);
            $sheet1->getCell('G19')->setValue($request['bu_area']);
            $sheet1->getCell('E39')->setValue($request['existing_residential_members']);
            $sheet1->getCell('E43')->setValue($request['existing_residential_members']);

            $sheet2 = $sheets[1];
            $sheet2->getCell('H2')->setValue($request['ready_reckoner_rate']);
            $sheet2->getCell('H36')->setValue($request['bmc_approval_cost']);
            $sheet2->getCell('H47')->setValue($request['residential_rent_for_one_month']);

            $sheet3 = $sheets[2];
            $sheet3->getCell('G7')->setValue($request['existing_carpet_area']);
            $sheet3->getCell('F8')->setValue($request['percentage_of_additional_carpet_area']);
            $sheet3->getCell('G14')->setValue($request['sale_rate_considered']);

            $this->data = [
                'source' => $inputFileName,
                'balance_area_of_plot' => $sheet1->getCell('H3')->getCalculatedValue(),
                'total_recovery' => $sheet3->getCell('G18')->getCalculatedValue(),
                'balance' => $sheet3->getCell('G20')->getCalculatedValue(),
                'stamp_duty_and_registration_charges' => $sheet3->getCell('G21')->getCalculatedValue(),
                'net_balance' => $sheet3->getCell('G23')->getCalculatedValue()
            ];
        }
        catch (\Exception $e) {
            $this->message = $e->getMessage();
        }

        return $this->sendResponse();
    }

}
