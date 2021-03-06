<?php
// app/Http/Controllers/PostController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Session;

use \App\Models\LookupType as LookupType;
use \App\Models\Staff as Staff;
use \App\Models\User as User;
use \App\Models\Equipment as Equipment;

class StaffController extends Controller {

    public function __construct() {
        //$this->middleware(['auth', 'clearance'])->except('index', 'show');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index($pagetype) {
		$where_clause = '';
		
		if(Auth::user()->hasRole('hub_coordinator')){
			//$staff = Staff::where('hubid',Auth::user()->hubid)->Orderby('id', 'desc')->where('type', $pagetype)->paginate(10);
			$where_clause = "AND s.hubid = '".Auth::user()->hubid."'";
		}else{
        	//$staff = Staff::Orderby('id', 'desc')->where('type', $pagetype)->paginate(10);
		}
		$query = "SELECT s.id, s.firstname, s.lastname, s.designation, s.hasdrivingpermit, s.hasbbtraining, s.isimmunizedforhb, s.hasdefensiveriding, s.permitexpirydate, s.nationalid, f.name as facility 
		FROM staff as s 
		LEFT JOIN facility as f ON (s.hubid = f.id) 
		WHERE s.designation = ".$pagetype." ".$where_clause."
		ORDER BY s.firstname ASC";
		//echo $query;
		//exit;
		$staff = \DB::select($query);
		return view('staff.index', compact('staff', 'pagetype'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    
	public function form($pagetype){
		$lt = new LookupType();
		$lt->name = 'DESIGNATIONS';
		$hubsdropdown = getAllHubs();
		$bikes = array_merge_maintain_keys(array('' => 'Select one'),getAllUnAssignedBikes());
		$designation = array_merge_maintain_keys(array('' => 'Select one'),$lt->getOptionValuesAndDescription());
		$lt->name = 'YES_NO';
		$yes_no = $lt->getOptionValuesAndDescription();
		return view('staff.create', compact('pagetype','designation', 'hubsdropdown','bikes','yes_no'));
	}
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) { 
		//\Validator::make(['drivingpermit' => 'unique'], ['nationalid' => 'unique'])->passes();
		//  $this->validate($request, [
		// 	'drivingpermit' => 'nullable|required|max:8|unique:staff',
		// 	'nationalid' => 'nullable|unique:staff'
		// ]);
		
		$staff = new Staff;
		try {
			//check that the rider being added is not already added
			$staff->facilityid = $request->facilityid;
			if(Auth::user()->hasRole('hub_coordinator')){
				$staff->hubid = Auth::user()->hubid;
			}else{
				$staff->hubid = $request->facilityid;
			}
			$staff->type = $request->type;
			$staff->motorbikeid = $request->motorbikeid;
			$staff->isactive = 1;
			$staff->firstname = $request->firstname;
			$staff->lastname = $request->lastname;
			$staff->othernames = $request->othernames;
			$staff->emailaddress = $request->emailaddress;
			$staff->telephonenumber = $request->telephonenumber;
			$staff->nationalid = $request->nationalid;
			$staff->code = $request->code;
			
			$staff->motorbikeid = $request->motorbikeid;
			$staff->type = $request->type;
			$staff->designation = $request->type;
			if($request->type == 1){
				$staff->hasdrivingpermit = $request->hasdrivingpermit;
				$staff->hasdefensiveriding = $request->hasdefensiveriding;
				$staff->hasbbtraining = $request->hasbbtraining;
				$staff->permitexpirydate = getMysqlDateFormat($request->permitexpirydate);
				$staff->isimmunizedforhb = $request->isimmunizedforhb;
			}
			$staff->save();
			//now add the transporter to the bike, if adding sample transporter
			if($request->type == 1){
				if(!empty($request->motorbikeid)){
					$bike = Equipment::findOrFail($request->motorbikeid);
					$bike->sampletransporterid = $staff->id;
					$bike->save();
				}
			}
		
		//create a user for the staff member
		$user = new User;
		if(empty($request->emailaddress)){
			$user->email = date('ymdhi').'@dev.com';
		}else{
			$user->email = $request->emailaddress;
		}
		$roles = array();
		if($request->type == 1){
			$roles = array(15);
		}elseif($request->type == 2 && Auth::user()->hubid == 2490){
			$roles = array(12);
		}elseif($request->type ==4){
			$roles = array(15);
		}elseif($request->type ==5){
			$roles = array(13);
		}

		$user->name = $request->firstname.' '.$request->lastname;
		$user->setPasswordAttribute('password');
		$user->hubid = Auth::user()->hubid;
		$user->username = $request->firstname;		
		$user->save();	
		$user->roles()->attach($roles);
		
		
		return redirect()->route('staff.show', array('id' => $staff->id));

		}catch (\Exception $e) {
			
			print_r('faild to save'.$e);
			exit;
			return redirect()->url('staff/new/'.$request->type)
            ->with('flash_message', 'failed');
		}
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
		$staff = Staff::findOrFail($id); //Find post of id = $id
        return view ('staff.show', compact('staff'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
		$staff = Staff::findOrFail($id); 
		$pagetype = $staff->type;
		$lt = new LookupType();
		$lt->name = 'DESIGNATIONS';
		$hubsdropdown = getAllHubs();
		$designation = array_merge_maintain_keys(array('' => 'Select one'),$lt->getOptionValuesAndDescription());
		$lt->name = 'YES_NO';
		$yes_no = $lt->getOptionValuesAndDescription();
		return view('staff.edit', compact('yes_no','staff','pagetype','designation', 'hubsdropdown'));
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
    	
		$staff = Staff::findOrFail($id);
		
		try {
			$staff->facilityid = $request->facilityid;
			$staff->hubid = $request->facilityid;
			$staff->firstname = $request->firstname;
			$staff->lastname = $request->lastname;
			$staff->othernames = $request->othernames;
			$staff->emailaddress = $request->emailaddress;
			$staff->telephonenumber = $request->telephonenumber;
			$staff->nationalid = $request->nationalid;
			
			if($staff->type == 1){
				$staff->hasdrivingpermit = $request->hasdrivingpermit;
				$staff->hasdefensiveriding = $request->hasdefensiveriding;
				$staff->hasbbtraining = $request->hasbbtraining;
				$staff->permitexpirydate = getMysqlDateFormat($request->permitexpirydate);
				$staff->isimmunizedforhb = $request->isimmunizedforhb;
			}else{
				$staff->designation = $request->designation;
			}
			$staff->save();
			return redirect()->route('staff.show', array('id' => $staff->id));

		}catch (\Exception $e) {
			
			print_r('faild to save'.$e);
			exit;
			return redirect()->url('staff/new/'.$request->type)
            ->with('flash_message', 'failed');
		}
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
       
    }
	
  /**
     *
     * @return \Illuminate\Http\Response - bikes for a hub which do not have a rider
     */
    public function bikeWithoutRider(Request $request){
		$hubid = $request->hubid;	
    	if($request->ajax()){
			$bikes = \App\Models\Equipment::where('hubid',$request->hubid)->whereDoesntHave('bikerider')->pluck("numberplate","id");
    		$html_options = getGenerateHtmlforAjaxSelect($bikes);
    		return response()->json(['options'=>$html_options]);
    	}

    }
}