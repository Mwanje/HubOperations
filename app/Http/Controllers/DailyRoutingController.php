<?php
namespace Khill\Lavacharts\Examples\Controllers;
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use \Lava;
use Auth;
use Session;

use \App\Models\LookupType as LookupType;
use \App\Models\DailyRouting as DailyRouting;
use \App\Models\Facility as Facility;
use \App\Models\Hub as Hub;
use \App\Models\DailyRoutingReason as DailyRoutingReason;
use \App\Models\Sample as Sample;
use \App\Models\Result as Result;

class DailyRoutingController extends Controller {

    public function __construct() {
        //$this->middleware(['auth', 'clearance'])->except('index', 'show');
    }
	
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index() {
        $staff = RoutingSchedule::Orderby('id', 'desc')->paginate(10);
		return view('routingschedule.list');
    }
	public function sampleList(Request $request){
			// get the samples for this month
			$hubs = array_merge_maintain_keys(array('' => 'Select a hub'), getAllHubs());
			$facilities = array_merge_maintain_keys(array('' => 'Select a facility'), getAllFacilities());
			$districts = array_merge_maintain_keys(array('' => 'Select a district'), getAllDistricts());
			
			$incharge_clause = '';
			if(Auth::user()->hasRole('hub_coordinator')){
				$incharge_clause .= " AND s.hubid = '".Auth::user()->hubid."'";
				$facilities = array_merge_maintain_keys(array(''=>'Select a facility'),getFacilitiesforHub(Auth::user()->hubid));
			}
			$graph_title = 'Samples last month';
			$where_clause = '';
			$where = 'WHERE MONTH(thedate) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH)';
			$filters = $request->all();
			if(!empty($filters)){
				$graph_title = 'Samples for selected options';
				if($filters['from'] != '' && $filters['to'] != ''){
					$where = "WHERE s.thedate BETWEEN '".getMysqlDateFormat($filters['from'])."'  AND '".getMysqlDateFormat($filters['to'])."'";
				}
			
				if(array_key_exists('facilityid',$filters) && $filters['facilityid']){
					$where_clause .= ' AND s.facilityid = '.$filters['facilityid'];
				}
				if(array_key_exists('hubid',$filters) && $filters['hubid']){
					$where_clause .= ' AND h.id = '.$filters['hubid'];
				}
				if(array_key_exists('districtid',$filters) && $filters['districtid']){
					$where_clause .= ' AND d.id = '.$filters['districtid'];
				}
			}
			$query = "SELECT h.hubname as hub, d.name as district, f.name as facility,
	SUM(CASE WHEN s.samplecategory = 1 THEN s.numberofsamples END) AS `VL`,
	SUM(CASE WHEN s.samplecategory = 2 THEN s.numberofsamples  END) AS `HIVEID`,
	SUM(CASE WHEN s.samplecategory = 3 THEN s.numberofsamples  END) AS `SickleCell`,
	SUM(CASE WHEN s.samplecategory = 4 THEN s.numberofsamples  END) AS `CD4CD8`,
	SUM(CASE WHEN s.samplecategory = 5 THEN s.numberofsamples  END) AS `Genexpert`,
	SUM(CASE WHEN s.samplecategory = 6 THEN s.numberofsamples  END) AS `CBCFBC`,
	SUM(CASE WHEN s.samplecategory = 7 THEN s.numberofsamples  END) AS `LFTS`,
	SUM(CASE WHEN s.samplecategory = 8 THEN s.numberofsamples  END) AS `RFTS`,
	SUM(CASE WHEN s.samplecategory = 9 THEN s.numberofsamples  END) AS `Culturesensitivity`,
	SUM(CASE WHEN s.samplecategory = 10 THEN s.numberofsamples  END) AS `MTBCultureDST`,
	SUM(s.numberofsamples) as total
	FROM samples s  
	LEFT JOIN  facility f ON (f.id = s.facilityid)
	LEFT JOIN  facility AS h ON (f.parentid = h.id)
	LEFT JOIN  district AS d ON (f.districtid = d.id)
	 ".$where.$where_clause.$incharge_clause."
	GROUP BY s.facilityid ASC";
	
	$samples = \DB::select($query);
		 
		$query = "SELECT lv.lookupvaluedescription as sampletype, SUM(s.numberofsamples) AS samples
	FROM samples s 
	INNER JOIN lookuptypevalue lv ON(lv.lookuptypevalue = s.samplecategory)
	INNER JOIN lookuptype l ON(l.id = lv.lookuptypeid AND lv.lookuptypeid = 18 )
	".$where.$where_clause.$incharge_clause."
	GROUP BY lv.lookupvaluedescription ASC";
		   $samples_graph = \DB::select($query);
		 
		 $samplestable = lava::DataTable();
		$samplestable->addStringColumn('Sample Type')
				->addNumberColumn('Number of samples');
			foreach($samples_graph as $line){
				$samplestable->addRow([
				  $line->sampletype, $line->samples
				]);
			}
			//$chart = lava::LineChart('samples', $stocksTable);			
			lava::ColumnChart('samples', $samplestable, [
				'title' => $graph_title,
				'titleTextStyle' => [
					'color'    => '#eb6b2c',
					'fontSize' => 14
				]
			]);
			
		return view('dailyrouting.samplelist', compact('samples', 'hubs', 'facilities','districts', 'samples_graph'));
	}
	/*
	*
	*The results page and graph
	*/
	public function resultList(Request $request){
			
			$hubs = array_merge_maintain_keys(array('' => 'Select a hub'), getAllHubs());
			$facilities = array_merge_maintain_keys(array('' => 'Select a facility'), getAllFacilities());
			$districts = array_merge_maintain_keys(array('' => 'Select a district'), getAllDistricts());
			//
			$incharge_clause = '';
			if(Auth::user()->hasRole('hub_coordinator')){
				$incharge_clause .= " AND s.hubid = '".Auth::user()->hubid."'";
				$facilities = array_merge_maintain_keys(array(''=>'Facility'),getFacilitiesforHub(Auth::user()->hubid));
			}
			$where_clause = '';
			$graph_title = 'Results last month';
			$where = 'WHERE MONTH(thedate) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH)';
			$filters = $request->all();
			if(!empty($filters)){
				if($filters['from'] != '' && $filters['to'] != ''){
					$where = "WHERE s.thedate BETWEEN '".getMysqlDateFormat($filters['from'])."'  AND '".getMysqlDateFormat($filters['to'])."'";
				}
				$graph_title = 'Results for selected options';
				if(array_key_exists('facilityid',$filters) && $filters['facilityid']){
					$where_clause .= ' AND s.facilityid = '.$filters['facilityid'];
				}
				if(array_key_exists('hubid',$filters) && $filters['hubid']){
					$where_clause .= ' AND h.id = '.$filters['hubid'];
				}
				if(array_key_exists('districtid',$filters) && $filters['districtid']){
					$where_clause .= ' AND d.id = '.$filters['districtid'];
				}
			}
			$query = "SELECT h.hubname as hub, d.name as district, f.name as facility,
	SUM(CASE WHEN s.samplecategory = 1 THEN s.numberofresults END) AS `VL`,
	SUM(CASE WHEN s.samplecategory = 2 THEN s.numberofresults  END) AS `HIVEID`,
	SUM(CASE WHEN s.samplecategory = 3 THEN s.numberofresults  END) AS `SickleCell`,
	SUM(CASE WHEN s.samplecategory = 4 THEN s.numberofresults  END) AS `CD4CD8`,
	SUM(CASE WHEN s.samplecategory = 5 THEN s.numberofresults  END) AS `Genexpert`,
	SUM(CASE WHEN s.samplecategory = 6 THEN s.numberofresults  END) AS `CBCFBC`,
	SUM(CASE WHEN s.samplecategory = 7 THEN s.numberofresults  END) AS `LFTS`,
	SUM(CASE WHEN s.samplecategory = 8 THEN s.numberofresults  END) AS `RFTS`,
	SUM(CASE WHEN s.samplecategory = 9 THEN s.numberofresults  END) AS `Culturesensitivity`,
	SUM(CASE WHEN s.samplecategory = 10 THEN s.numberofresults  END) AS `MTBCultureDST`,
	SUM(s.samplecategory) as total
	FROM results s  
	LEFT JOIN  facility f ON (f.id = s.facilityid)
	LEFT JOIN  facility AS h ON (f.parentid = h.id)
	LEFT JOIN  district AS d ON (f.districtid = d.id)
	 ".$where.$where_clause.$incharge_clause."
	GROUP BY s.facilityid ASC";
	$results = \DB::select($query);
		 
		$query = "SELECT lv.lookupvaluedescription as resulttype, SUM(s.numberofresults) AS results
	FROM results s 
	INNER JOIN lookuptypevalue lv ON(lv.lookuptypevalue = s.samplecategory)
	INNER JOIN lookuptype l ON(l.id = lv.lookuptypeid )
	".$where.$where_clause.$incharge_clause." AND lv.lookuptypeid = 18
	GROUP BY lv.lookupvaluedescription ASC";
	
		   $result_graph = \DB::select($query);		
			
		$resultstable = lava::DataTable();
		$resultstable->addStringColumn('Result Type')
					->addNumberColumn('Number of results');
				 foreach($result_graph  as $line){
					$resultstable->addRow([
					  $line->resulttype, $line->results
					]);
				}
		
		lava::ColumnChart('theresults', $resultstable, [
			'title' => $graph_title,
			'titleTextStyle' => [
				'color'    => '#eb6b2c',
				'fontSize' => 14
			]
		]);
			
		return view('dailyrouting.resultlist', compact('results', 'hubs', 'facilities','districts', 'result_graph'));
	}

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    
	public function createform($thedate, $facilityid, $bikeid, $tranpsorterid){
		$hubid = Auth::getUser()->hubid; 
		$facilitydropdown = array_merge_maintain_keys(array(''=>"Facility"),getFacilitiesforHub($hubid));
		$bikes = array_merge_maintain_keys(array(''=>"Motorcycle"), getAssignedBikesforHub($hubid));
		$transporters = array_merge_maintain_keys(array('' => 'Transporter' ),getSampleTransportersforHub($hubid));
		$lt = new LookupType;
		$lt->name = 'ROUTE_REASONS';
		$routereasons = $lt->getOptionValuesAndDescription();
		
		$lt->name = 'SAMPLE_CATEGORIES';
		$samplecategories = $lt->getOptionValuesAndDescription();
		//$pagetype = $type;
		return view('dailyrouting.create', compact('samplecategories','routereasons','bikes','transporters','facilitydropdown', 'thedate', 'facilityid', 'bikeid', 'tranpsorterid'));
	}
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) { 
	$samples = $request->sample;
	$hubid = Auth::user()->hubid;
		//	
		try {
		   \DB::transaction(function() use($request, $hubid){
			   try {
					$dailyrouting = new DailyRouting;
					$dailyrouting->hubid = $hubid;
					$dailyrouting->bikeid = $request->bikeid;
					$dailyrouting->transporterid = $request->transporterid;
					$dailyrouting->thedate = getMysqlDateFormat($request->thedate);
					$dailyrouting->createdby = Auth::user()->id;
					//now save the resaons for routing
					$dailyrouting->save();
				}catch (\Exception $e) {
					print_r($e->getMessage());
					exit;
				}
				$reasons = $request->reasons;
				foreach($reasons as $reasonid => $reason){
					try{
						$routingreason = new DailyRoutingReason;
						$routingreason->hubid = $hubid;
						$routingreason->facilityid = $request->facilityid;
						$routingreason->reason = $reasonid;
						$routingreason->thedate = getMysqlDateFormat($request->thedate);
						$routingreason->createdby = Auth::user()->id;
						$routingreason->save();
					}catch (\Exception $e) {
						print_r($e->getMessage());
						exit;
					}
				}
				//now save the rounting details
				$samples = $request->samplecategories;
				foreach($samples as $key => $value){
					if($value['sample'] || $value['result']){
						try{
							if($value['sample']){
								$sample = new Sample;
								$sample->samplecategory = $key;
								$sample->numberofsamples = $value['sample'];
								$sample->dailyroutingid = $dailyrouting->id;
								$sample->hubid = $hubid;
								$sample->facilityid = $request->facilityid;
								$sample->bikeid = $request->bikeid;
								$sample->transporterid = $request->transporterid;
								$sample->thedate = getMysqlDateFormat($request->thedate);
								$sample->createdby = Auth::user()->id;
								$sample->save();
							}
							if($value['result']){
								$sample = new Result;
								$sample->samplecategory = $key;
								$sample->numberofresults = $value['result'];
								$sample->dailyroutingid = $dailyrouting->id;
								$sample->hubid = $hubid;
								$sample->facilityid = $request->facilityid;
								$sample->bikeid = $request->bikeid;
								$sample->transporterid = $request->transporterid;
								$sample->thedate = getMysqlDateFormat($request->thedate);
								$sample->createdby = Auth::user()->id;
								$sample->save();
							}
						}catch (\Exception $e) {
							print_r($e->getMessage());
							exit;
						}
					}
				}
				
			});
			//echo 'bifu';
			//exit;
			return redirect()->route('dailyrouting.view', array('date' =>  getMysqlDateFormat($request->thedate), 
										'hubid' => $hubid));
		}catch (\Exception $e) {
			echo $e->getMessage(); 
			exit;
			return redirect()->route('dailyrouting.create')
            ->with('flash_message', 'failed');
		}
    }
	
	public function view($thedate, $hubid){
		$hub = Facility::findOrFail($hubid);
		$bikes = getAssignedBikesforHub($hubid);
		$bikes_counter = 1;
		return View('dailyrouting.view', compact('bikes','thedate','bikes_counter','hub'));
		//echo 'nothing to show';
		//exit('viewing');
	}
    

    /**tuesdayschedule
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
		$hub = Facility::findOrFail($id);
		$facilitydropdown = getFacilitiesforHub($id);
		$facilities = Facility::where('parentid', $id)->get();
		$mondayfacilityids = getHubScheduleFacilitiesforaDay(1, $id);
		//print_r($mondayfacilityids); exit;
		/*foreach($facilities as $facility){
			echo $facility->id.'<br>';
			if(in_array($facility->id, $mondayfacilityids)){
				echo 'true<br>';
			}
		}*/
		//exit;
		$tuesdayfacilityids = getHubScheduleFacilitiesforaDay(2, $id);
		$wednesdayfacilityids = getHubScheduleFacilitiesforaDay(3, $id);
		$thursdayfacilityids = getHubScheduleFacilitiesforaDay(4, $id);
		$fridayfacilityids = getHubScheduleFacilitiesforaDay(5, $id);
		$saturdayfacilityids = getHubScheduleFacilitiesforaDay(6, $id);
		$sundayfacilityids = getHubScheduleFacilitiesforaDay(7, $id);
		//exit;
		return view('routingschedule.edit', compact('facilitydropdown','mondayfacilityids','tuesdayfacilityids','wednesdayfacilityids','thursdayfacilityids','fridayfacilityids','saturdayfacilityids','sundayfacilityids', 'facilities', 'hub'));
        
    }
	/*
	* Checks whether a date already has data added
	*/
	public function checkDateData(Request $request){
		return redirect()->route('dailyrouting.createform',['thedate' => getMysqlDateFormat($request->thedate), 'facilityid' => $request->facilityid, 'bikeid' => $request->bikeid, 'transporterid' => $request->transporterid]);
	}
	
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
		try {
		   \DB::transaction(function() use($request, $id){
			  //delete the previously existing data
			\DB::table('routingschedule')->where('hubid', $id)->delete();
			//now save the updated/new schedule 
			   //save monday schedule
			   $monday_data = $request->monday;
			   if(count($monday_data)){
					for($i = 0; $i < count($request->monday); $i++){
						$routingschedule = new RoutingSchedule;
						$routingschedule->hubid = Auth::getUser()->hubid;
						$routingschedule->facilityid = $monday_data[$i];
						$routingschedule->dayoftheweek = 1;
						$routingschedule->createdby = Auth::getUser()->id;
						$routingschedule->save();
					}
				}
				$tuesday_data = $request->tuesday;
			   if(count($tuesday_data)){
					for($i = 0; $i < count($tuesday_data); $i++){
						$routingschedule = new RoutingSchedule;
						$routingschedule->hubid = Auth::getUser()->hubid;
						$routingschedule->facilityid = $tuesday_data[$i];
						$routingschedule->dayoftheweek = 2;
						$routingschedule->createdby = Auth::getUser()->id;
						$routingschedule->save();
					}
				}
				$wednesday_data = $request->wednesday;
			   if(count($wednesday_data)){
					for($i = 0; $i < count($wednesday_data); $i++){
						$routingschedule = new RoutingSchedule;
						$routingschedule->hubid = Auth::getUser()->hubid;
						$routingschedule->facilityid = $wednesday_data[$i];
						$routingschedule->dayoftheweek = 3;
						$routingschedule->createdby = Auth::getUser()->id;
						$routingschedule->save();
					}
				}
				$thursday_data = $request->thursday;
			   if(count($thursday_data)){
					for($i = 0; $i < count($request->thursday); $i++){
						$routingschedule = new RoutingSchedule;
						$routingschedule->hubid = Auth::getUser()->hubid;
						$routingschedule->facilityid = $thursday_data[$i];
						$routingschedule->dayoftheweek = 4;
						$routingschedule->createdby = Auth::getUser()->id;
						$routingschedule->save();
					}
				}
				$friday_data = $request->friday;
			   if(count($friday_data)){
					for($i = 0; $i < count($friday_data); $i++){
						$routingschedule = new RoutingSchedule;
						$routingschedule->hubid = Auth::getUser()->hubid;
						$routingschedule->facilityid = $friday_data[$i];
						$routingschedule->dayoftheweek = 5;
						$routingschedule->createdby = Auth::getUser()->id;
						$routingschedule->save();
					}
				}
				$saturday_data = $request->saturday;
			   if(count($saturday_data)){
					for($i = 0; $i < count($saturday_data); $i++){
						$routingschedule = new RoutingSchedule;
						$routingschedule->hubid = Auth::getUser()->hubid;
						$routingschedule->facilityid = $saturday_data[$i];
						$routingschedule->dayoftheweek = 6;
						$routingschedule->createdby = Auth::getUser()->id;
						$routingschedule->save();
					}
				}
				$sunday_data = $request->sunday;
			   if(count($sunday_data)){
					for($i = 0; $i < count($sunday_data); $i++){
						$routingschedule = new RoutingSchedule;
						$routingschedule->hubid = Auth::getUser()->hubid;
						$routingschedule->facilityid = $sunday_data[$i];
						$routingschedule->dayoftheweek = 7;
						$routingschedule->createdby = Auth::getUser()->id;
						$routingschedule->save();
					}
				}
			});
			//echo 'bifu';
			//exit;
			//send the hubid instead so that you can pick schedule for the hub
			return redirect()->route('routingschedule.show', array('id' => Auth::getUser()->hubid));
		}catch (\Exception $e) {
			print_r('faild to save'.$e);
			exit;
			return redirect()->route('routingschedule.create')
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
}