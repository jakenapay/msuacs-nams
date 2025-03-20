<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/userguide3/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
// $route['default_controller'] = 'VisitorsFormController/welcome_page';
$route['default_controller'] = 'DashboardController/index';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
$route['errors/blocked'] = 'AuthController/blocked';
$route['receiveData'] = 'ApiForm/receiveData';

$route['form'] = 'VisitorsFormController/step1';
//$route['form'] = 'VisitorsFormController/step1';
$route['form2'] = 'VisitorsFormController/visitRequestForm';
$route['visitRequest'] = 'VisitorsFormController/visitRequestForm';
$route['visitors_pending/pending'] = 'VisitorsFormController/pending';

$route['form/status'] = 'VisitorsFormController/status_check';
$route['form/verify_status'] = 'VisitorsFormController/verify_status';
$route['form/reservation/update/(:num)'] = 'VisitorsFormController/reservation_update/$1';
$route['form/reservation/cancel/(:num)'] = 'VisitorsFormController/reservation_cancel/$1';
$route['form/reservation/confirm'] = 'VisitorsFormController/otpConfirmationView';
$route['form/reservation/verify_otp'] = 'VisitorsFormController/verifyReservationOTP';
$route['form/reservation/resend_otp'] = 'VisitorsFormController/resendReservationOTP';

// API FOR VISITORS PENDING 
$route['api/visitors_pending'] = 'VisitorsFormController/index';
$route['api/visitors_pending/(:num)'] = 'VisitorsFormController/show/$1';
$route['api/visitors_pending/create'] = 'VisitorsFormController/store';
$route['api/visitors_pending/update/(:num)'] = 'VisitorsFormController/update/$1';
$route['api/visitors_pending/delete/(:num)'] = 'VisitorsFormController/destroy/$1';

//STEP 1 DISPLAY VIEW AND PROCESS FORM
$route['visitors_pending/form/step1'] = 'VisitorsFormController/step1';
$route['visitors_pending/process_step1'] = 'VisitorsFormController/process_step1';
//STEP2
$route['visitors_pending/form/step2'] = 'VisitorsFormController/step2';
$route['visitors_pending/process_step2'] = 'VisitorsFormController/process_step2';
//STEP3
$route['visitors_pending/form/step3'] = 'VisitorsFormController/step3';
$route['visitors_pending/process_step3'] = 'VisitorsFormController/process_step3';
//STEP4
$route['visitors_pending/form/step4'] = 'VisitorsFormController/step4';
$route['visitors_pending/process_step4'] = 'VisitorsFormController/process_step4';
$route['visitors_pending/get/contact_person'] = 'VisitorsFormController/get_contact_persons_by_position';
//INPUT REVIEW
$route['visitors_pending/form/review'] = 'VisitorsFormController/show_review';
//PROCESS FINAL FORM DATA AFTER REVIEW  
$route['visitors_pending/form/final_process'] = 'VisitorsFormController/process_final';
//STEP5
$route['visitors_pending/form/step5'] = 'VisitorsFormController/step5';
$route['visitors_pending/form/verify_otp'] = 'VisitorsFormController/verifyOTP';
$route['visitors_pending/form/resend_otp'] = 'VisitorsFormController/resendOTP';
//FINAL STEP (SUCCESS)
// $route['visitors_pending/form/success'] = 'VisitorsFormController/success';


// $route['send_otp'] = 'VisitorsPendingController/sendOTP';

//DASHBOARD PAGES
$route['admin/login'] ='AuthController/index';
$route['admin'] ='AuthController/index';
$route['admin/verify'] = 'AuthController/login';
$route['admin/logout'] = 'AuthController/logout';

//ADMIN ACCOUNTS MANAGEMENT 
$route['admin/admin_management/accounts'] = 'AdminManagementController/index';
$route['admin/admin_management/accounts/list'] = 'AdminManagementController/adminList';
$route['admin/admin_management/accounts/add/view'] = 'AdminManagementController/addAdminView';
$route['admin/admin_management/accounts/add'] = 'AdminManagementController/addAdmin';
$route['admin/admin_management/accounts/edit/view/(:num)'] = 'AdminManagementController/editAdminView/$1';
$route['admin/admin_management/accounts/edit/(:num)'] = 'AdminManagementController/editAdmin/$1';
$route['admin/admin_management/accounts/view/(:num)'] = 'AdminManagementController/adminView/$1';
$route['admin/admin_management/accounts/delete/(:num)'] = 'AdminManagementController/verifyDeleteAdmin/$1';

//ADMIN ROLES
$route['admin/admin_management/permissions'] = 'AdminManagementController/index';
$route['admin/admin_management/permissions/add/view'] = 'AdminManagementController/addAdminView';
$route['admin/admin_management/permissions/add'] = 'AdminManagementController/addAdmin';

//DASHBOARD HOME PAGE
$route['admin/dashboard'] = 'DashboardController/index';
$route['admin/live_devices'] = 'DashboardController/liveDevices';
$route['admin/ping'] = 'DashboardController/test';
$route['admin/dashboard/filter_user_type_distribution'] = 'DashboardController/filterUserTypeDistribution';
$route['admin/dashboard/filter_exit_trends'] = 'DashboardController/filterExitTrends';
$route['admin/dashboard/filter_visitor_trends'] = 'DashboardController/filterEntryTrends';
$route['admin/dashboard/get_peak_hours_data'] = 'DashboardController/getPeakHoursData';
$route['admin/dashboard/filter_total_entry_exit'] = 'DashboardController/filterTotalEntryExit';

//SECURITY LOGS
$route['admin/security/security_logs'] = 'SecurityController/securityLogsView';
$route['admin/security/id_logs'] = 'SecurityController/idLogsView';
$route['admin/security/security_logs_list'] = 'SecurityController/securityLogsList';
$route['admin/security/id_logs_list'] = 'SecurityController/idLogsList';
$route['admin/security/security_logs_list_filter'] = 'SecurityController/securityLogsListFilter';
$route['admin/security/id_logs_list_filter'] = 'SecurityController/idLogsListFilter';
$route['admin/security/add_new_id_log'] = 'SecurityController/idLogsAddModal';
$route['admin/security/add_id_log'] = 'SecurityController/addIdLog';

//VISITOR PENDING
$route['admin/visit_management/visitors_pending'] = 'VisitorsPendingController/index';
$route['admin/visit_management/visitors_pending_list'] = 'VisitorsPendingController/visitorsPendingList';
$route['admin/visit_management/visitors_pending/view/(:num)'] = 'VisitorsPendingController/editVisitorView/$1';
$route['admin/visit_management/visitors_pending/add/view'] = 'VisitorsPendingController/addVisitorView';
$route['admin/visit_management/visitors_pending/add'] = 'VisitorsPendingController/addVisitor';
$route['admin/visit_management/visitors_pending/update/(:num)'] = 'VisitorsPendingController/updateVisitor/$1';
$route['admin/visit_management/visitors_pending/delete/(:num)'] = 'VisitorsPendingController/deleteVisitor/$1';
$route['admin/visit_management/visitors_pending/decline/(:num)'] = 'VisitorsPendingController/declineVisitor/$1';
$route['admin/visit_management/visitors_pending/approve/(:num)'] = 'VisitorsPendingController/approveVisitor/$1';

//VISITOR ACTIVE
$route['admin/visit_management/visitors_active'] = 'VisitorsActiveController/index';
$route['admin/visit_management/visitors_active/list'] = 'VisitorsActiveController/visitorsActiveList';
$route['admin/visit_management/visitors_active/view/(:num)'] = 'VisitorsActiveController/editActiveVisitorView/$1';
$route['admin/visit_management/visitors_active/update/(:num)'] = 'VisitorsActiveController/editActiveVisitor/$1';
$route['admin/visit_management/visitors_active/add/view'] = 'VisitorsActiveController/addVisitorView';
$route['admin/visit_management/visitors_active/add'] = 'VisitorsActiveController/addVisitor';
$route['admin/visit_management/visitors_active/delete/(:num)'] = 'VisitorsActiveController/verifyDeleteVisitor/$1';
$route['admin/visit_management/visitors_active/ban/(:num)'] = 'VisitorsActiveController/verifyBanVisitor/$1';
$route['admin/visit_management/visitors_active/unban/(:num)'] = 'VisitorsActiveController/verifyUnbanVisitor/$1';
$route['admin/visit_management/visitors_active/approve/(:num)'] = 'VisitorsActiveController/concludeVisitor/$1';
$route['admin/visit_management/visitors_active/getBannedLocations/(:num)'] = 'VisitorsActiveController/getBannedLocationsVisitors/$1';

//VISITORS CONCLUDED
$route['admin/visit_management/visitors_concluded'] = 'VisitorsCompletedController/index';
$route['admin/visit_management/visitors_concluded/list'] = 'VisitorsCompletedController/completedList';
$route['admin/visit_management/visitors_concluded/view/(:num)'] = 'VisitorsCompletedController/viewVisitor/$1';
$route['admin/visit_management/visitors_concluded/delete/(:num)'] = 'VisitorsCompletedController/verifyDeleteVisitor/$1';

//VISITORS DECLINED
$route['admin/visit_management/visitors_declined'] = 'VisitorsDeclinedController/index';
$route['admin/visit_management/visitors_declined/list'] = 'VisitorsDeclinedController/declinedList';
$route['admin/visit_management/visitors_declined/view/(:num)'] = 'VisitorsDeclinedController/viewVisitor/$1';
$route['admin/visit_management/visitors_declined/delete/(:num)'] = 'VisitorsDeclinedController/verifyDeleteVisitor/$1';
$route['admin/visit_management/visitors_card'] = 'VisitorsCards/index';


//STUDENTS DASHBOARD
$route['admin/user_management/students'] = 'UserManagementController/index';
$route['admin/user_management/students/list'] = 'UserManagementController/studentsList';
$route['admin/user_management/students/add'] = 'UserManagementController/addStudentView';
$route['admin/user_management/students/add_student'] = 'UserManagementController/addStudent';
$route['admin/user_management/students/view/(:num)'] = 'UserManagementController/editStudentView/$1';
$route['admin/user_management/students/edit/(:num)'] = 'UserManagementController/editStudent/$1';
$route['admin/user_management/students/update/(:num)'] = 'UserManagementController/updateStudent/$1';
$route['admin/user_management/students/ban/(:num)'] = 'UserManagementController/verifyBanStudent/$1';
$route['admin/user_management/students/unban/(:num)'] = 'UserManagementController/verifyUnbanStudent/$1';
$route['admin/user_management/students/delete/(:num)'] = 'UserManagementController/verifyDeleteStudent/$1';
$route['admin/user_management/students/excel_import_form'] = 'ExcelImportController/studentExcelImportForm';
$route['admin/user_management/students/excel_import'] = 'ExcelImportController/importStudent';
$route['admin/user_management/students/getBannedLocations/(:num)'] = 'UserManagementController/getBannedLocationsStudents/$1';

//RETRIEVE COLLEGES AND DEPARTMENTS
$route['admin/user_management/get/department_by_college/(:any)'] = 'UserManagementController/getDepartmentsByCollege/$1';
$route['admin/user_management/get/programs_by_department/(:any)'] = 'UserManagementController/getProgramsByDepartment/$1';

$route['admin/user_management/get/all_programs'] = 'UserManagementController/getAllPrograms';

//FACULTY DASHBOARD
$route['admin/user_management/faculty'] = 'UserManagementController/faculty';
$route['admin/user_management/faculty/list'] = 'UserManagementController/facultyList';
$route['admin/user_management/faculty/add'] = 'UserManagementController/addFacultyView';
$route['admin/user_management/faculty/add_faculty'] = 'UserManagementController/addFaculty';
$route['admin/user_management/faculty/view/(:num)'] = 'UserManagementController/editFacultyView/$1';
$route['admin/user_management/faculty/edit/(:num)'] = 'UserManagementController/editFaculty/$1';
$route['admin/user_management/faculty/update/(:num)'] = 'UserManagementController/updateFaculty/$1';
$route['admin/user_management/faculty/ban/(:num)'] = 'UserManagementController/verifyBanFaculty/$1';
$route['admin/user_management/faculty/unban/(:num)'] = 'UserManagementController/verifyUnbanFaculty/$1';
$route['admin/user_management/faculty/delete/(:num)'] = 'UserManagementController/verifyDeleteFaculty/$1';
$route['admin/user_management/faculty/excel_import_form'] = 'ExcelImportController/facultyExcelImportForm';
$route['admin/user_management/faculty/excel_import'] = 'ExcelImportController/importFaculty';
$route['admin/user_management/faculty/getBannedLocations/(:num)'] = 'UserManagementController/getBannedLocationsFaculty/$1';

//STAFF DASHBOARD
$route['admin/user_management/staff'] = 'UserManagementController/staff';
$route['admin/user_management/staff/list'] = 'UserManagementController/staffList';
$route['admin/user_management/staff/add'] = 'UserManagementController/addStaffView';
$route['admin/user_management/staff/add_staff'] = 'UserManagementController/addStaff';
$route['admin/user_management/staff/view/(:num)'] = 'UserManagementController/editStaffView/$1';
$route['admin/user_management/staff/edit/(:num)'] = 'UserManagementController/editStaff/$1';
$route['admin/user_management/staff/update/(:num)'] = 'UserManagementController/updateStaff/$1';
$route['admin/user_management/staff/ban/(:num)'] = 'UserManagementController/verifyBanStaff/$1';
$route['admin/user_management/staff/unban/(:num)'] = 'UserManagementController/verifyUnbanStaff/$1';
$route['admin/user_management/staff/delete/(:num)'] = 'UserManagementController/verifyDeleteStaff/$1';
$route['admin/user_management/staff/excel_import_form'] = 'ExcelImportController/staffExcelImportForm';
$route['admin/user_management/staff/excel_import'] = 'ExcelImportController/importStaff';
$route['admin/user_management/staff/getBannedLocations/(:num)'] = 'UserManagementController/getBannedLocationsStaff/$1';

//GUESTS DASHBOARD
$route['admin/user_management/residents'] = 'UserManagementController/residents';
$route['admin/user_management/residents/list'] = 'UserManagementController/residentsList';
$route['admin/user_management/residents/add/view'] = 'UserManagementController/addResidentView';
$route['admin/user_management/residents/add'] = 'UserManagementController/addResident';
$route['admin/user_management/residents/view/(:num)'] = 'UserManagementController/editResidentView/$1';
$route['admin/user_management/residents/edit/(:num)'] = 'UserManagementController/editResident/$1';
$route['admin/user_management/residents/update/(:num)'] = 'UserManagementController/updateResident/$1';
$route['admin/user_management/residents/ban/(:num)'] = 'UserManagementController/verifyBanResident/$1';
$route['admin/user_management/residents/unban/(:num)'] = 'UserManagementController/verifyUnbanResident/$1';
$route['admin/user_management/residents/delete/(:num)'] = 'UserManagementController/verifyDeleteResident/$1';
$route['admin/user_management/residents/excel_import_form'] = 'ExcelImportController/residentExcelImportForm';
$route['admin/user_management/residents/excel_import'] = 'ExcelImportController/importResident';
$route['admin/user_management/residents/getBannedLocations/(:num)'] = 'UserManagementController/getBannedLocationsResidents/$1';

//GUESTS DASHBOARD
$route['admin/user_management/guests'] = 'UserManagementController/guests';
$route['admin/user_management/guests/list'] = 'UserManagementController/guestsList';
$route['admin/user_management/guests/add/view'] = 'UserManagementController/addGuestView';
$route['admin/user_management/guests/add'] = 'UserManagementController/addGuest';
$route['admin/user_management/guests/view/(:num)'] = 'UserManagementController/editGuestView/$1';
$route['admin/user_management/guests/edit/(:num)'] = 'UserManagementController/editGuest/$1';
$route['admin/user_management/guests/update/(:num)'] = 'UserManagementController/updateGuest/$1';
$route['admin/user_management/guests/ban/(:num)'] = 'UserManagementController/verifyBanGuest/$1';
$route['admin/user_management/guests/unban/(:num)'] = 'UserManagementController/verifyUnbanGuest/$1';
$route['admin/user_management/guests/delete/(:num)'] = 'UserManagementController/verifyDeleteGuest/$1';
$route['admin/user_management/guests/excel_import_form'] = 'ExcelImportController/guestExcelImportForm';
$route['admin/user_management/guests/excel_import'] = 'ExcelImportController/importGuest';


//CONFIGURATIONS DASHBOARD
//Locations
$route['admin/configurations/locations'] = 'ConfigurationsController/index';
$route['admin/configurations/locations/list'] = 'ConfigurationsController/locationList';
$route['admin/configurations/locations/add/view'] = 'ConfigurationsController/addLocationView';
$route['admin/configurations/locations/add'] = 'ConfigurationsController/addLocation';
$route['admin/configurations/locations/edit/view/(:num)'] = 'ConfigurationsController/editLocationView/$1';
$route['admin/configurations/locations/edit/(:num)'] = 'ConfigurationsController/editLocation/$1';
$route['admin/configurations/locations/delete/(:num)'] = 'ConfigurationsController/verifyDeleteLocation/$1';

//Devices
$route['admin/configurations/devices'] = 'ConfigurationsController/devices';
$route['admin/configurations/devices/list'] = 'ConfigurationsController/deviceList';
$route['admin/configurations/devices/add/view'] = 'ConfigurationsController/addDeviceView';
$route['admin/configurations/devices/add'] = 'ConfigurationsController/addDevice';
$route['admin/configurations/devices/toggle_mode'] = 'ConfigurationsController/toggleModeView';
$route['admin/configurations/devices/toggle_mode/set'] = 'ConfigurationsController/toggleMode';
$route['admin/configurations/devices/edit/view/(:num)'] = 'ConfigurationsController/editDeviceView/$1';
$route['admin/configurations/devices/edit/(:num)'] = 'ConfigurationsController/editDevice/$1';
$route['admin/configurations/devices/delete/(:num)'] = 'ConfigurationsController/verifyDeleteDevice/$1';

//Colleges
$route['admin/configurations/colleges'] = 'ConfigurationsController/colleges';
$route['admin/configurations/colleges/list'] = 'ConfigurationsController/collegeList';
$route['admin/configurations/colleges/add/view'] = 'ConfigurationsController/addCollegeView';
$route['admin/configurations/colleges/add'] = 'ConfigurationsController/addCollege';
$route['admin/configurations/colleges/edit/view/(:num)'] = 'ConfigurationsController/editCollegeView/$1';
$route['admin/configurations/colleges/edit/(:num)'] = 'ConfigurationsController/editCollege/$1';
$route['admin/configurations/colleges/delete/(:num)'] = 'ConfigurationsController/verifyDeleteCollege/$1';

//Departments
$route['admin/configurations/departments'] = 'ConfigurationsController/departments';
$route['admin/configurations/departments/list'] = 'ConfigurationsController/departmentList';
$route['admin/configurations/departments/add/view'] = 'ConfigurationsController/addDepartmentView';
$route['admin/configurations/departments/add'] = 'ConfigurationsController/addDepartment';
$route['admin/configurations/departments/edit/view/(:num)'] = 'ConfigurationsController/editDepartmentView/$1';
$route['admin/configurations/departments/edit/(:num)'] = 'ConfigurationsController/editDepartment/$1';
$route['admin/configurations/departments/delete/(:num)'] = 'ConfigurationsController/verifyDeleteDepartment/$1';

//Programs
$route['admin/configurations/programs'] = 'ConfigurationsController/programs';
$route['admin/configurations/programs/list'] = 'ConfigurationsController/programList';
$route['admin/configurations/programs/getDepartmentsByCollege/(:num)'] = 'ConfigurationsController/getDepartmentsByCollege/$1';
$route['admin/configurations/programs/add/view'] = 'ConfigurationsController/addProgramView';
$route['admin/configurations/programs/add'] = 'ConfigurationsController/addProgram';
$route['admin/configurations/programs/edit/view/(:num)'] = 'ConfigurationsController/editProgramView/$1';
$route['admin/configurations/programs/edit/(:num)'] = 'ConfigurationsController/editProgram/$1';
$route['admin/configurations/programs/delete/(:num)'] = 'ConfigurationsController/verifyDeleteProgram/$1';

//Programs
$route['admin/configurations/offices'] = 'ConfigurationsController/offices';
$route['admin/configurations/offices/list'] = 'ConfigurationsController/officeList';
$route['admin/configurations/offices/add/view'] = 'ConfigurationsController/addOfficeView';
$route['admin/configurations/offices/add'] = 'ConfigurationsController/addOffice';
$route['admin/configurations/offices/edit/view/(:num)'] = 'ConfigurationsController/editOfficeView/$1';
$route['admin/configurations/offices/edit/(:num)'] = 'ConfigurationsController/editOffice/$1';
$route['admin/configurations/offices/delete/(:num)'] = 'ConfigurationsController/verifyDeleteOffice/$1';

//Entry Logs
$route['admin/logs/entry'] = 'LogsController/index';
$route['admin/logs/entry/list'] = 'LogsController/entryLogsList';
$route['admin/logs/entry/list/filter'] = 'LogsController/entryLogsListFilter';
$route['admin/logs/entry/location'] = 'LogsController/filterEntryLocation';

//Exit Logs
$route['admin/logs/exit'] = 'LogsController/exit';
$route['admin/logs/exit/list'] = 'LogsController/exitLogsList';
$route['admin/logs/exit/list/filter'] = 'LogsController/exitLogsListFilter';
$route['admin/logs/exit/location'] = 'LogsController/filterExitLocation';

//Reports
$route['admin/reports/logs'] = 'ReportsController/index';
$route['admin/reports/entry_logs/filter'] = 'ReportsController/filter';

//TEST ROUTES
$route['email'] = 'VisitorsPendingController/contact';


//SEEDER
$route['seeder'] = 'VisitorsPendingSeeder/index';
$route['seeder/students'] = 'StudentSeeder/index';
$route['seeder/roles'] = 'Seeders/AdminRolesSeeder/index';
$route['seeder/admin'] = 'Seeders/AdminSeeder/index';
$route['seeder/admin_roles'] = 'Seeders/RolesSeeder/index';


//API ROUTES
$route['api/access'] = 'API/access';
$route['api/exit'] = 'API/exit';

// API FOR EXPORTING DATA FOR LRS
$route['api/export/students'] = 'DataTransfer/exportStudents';
$route['api/getAllData/(:any)'] = 'DataTransfer/getAllData/$1';