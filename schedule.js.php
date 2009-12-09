<?php
        require_once('../../config.php');
        require_once($CFG->dirroot.'/blocks/parentseve/lib.php');
        $peid = required_param('peid', PARAM_INT); // Parents evening ID
?>
var i=0;
var appsdeleted=new Array();
function newAppointment(){
    var newapp=document.createElement('div');
    newapp.id='termreview_pe_appointment'+i;
    newapp.className='termreview_pe_appointment';
    newapp.innerHTML='<?php print_string('teacher','block_parentseve');?>:<select name="appointmentteacher['+i+']" onchange="termreview_pe_refreshSchedule('+i+')"><?php
        $teachers=termreview_get_parentseve_teachers($peid);
        foreach ($teachers as $teacher) {
            echo'<option value="'.$teacher->id.'">'.$teacher->firstname.' '.$teacher->lastname.'</option>';
        }
        ?></select><div class="termreview_pe_schedule" id="termreview_pe_schedule'+i+'"></div><a href="#" onclick="delAppointment('+i+')">Cancel</a>';
        document.getElementById('termreview_pe_appointments').appendChild(newapp);
        termreview_pe_refreshSchedule(i);
        i++;
}

function delAppointment(id){
    var todel=document.getElementById('termreview_pe_appointment'+id);
    var apps= document.getElementById('termreview_pe_appointments');
    apps.removeChild(todel);
    appsdeleted[id]=true;
}

function termreview_pe_refreshSchedule(id){
    var appointment=document.getElementById('termreview_pe_appointment'+id);
    var teacherid=appointment.childNodes[1].options[appointment.childNodes[1].selectedIndex].value;
    //have to trial and error to get right browser code
    var xmlHttp;
    try{
        // Firefox, Opera 8.0+, Safari
        xmlHttp=new XMLHttpRequest();
    }
    catch (e){
        // Internet Explorer
        try{
            xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
        }
        catch (e){
            try{
                xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
            }
            catch (e){
                alert("Your browser does not support AJAX!");
                return false;
            }
        }
    }

    xmlHttp.onreadystatechange=function(){
        if(xmlHttp.readyState==4){
          //We've got a schedule from the server
          var timeslots=xmlHttp.responseText.split("\n");

          //constuct a new schedule
          var newschedule='<table class="termreview_pe_schedule_form">';
          for(j=0;j<timeslots.length;j++){
              timeslot=timeslots[j].split(',');
              newschedule += '<tr><td>'+timeslot[0]+'</td><td>';
              if(timeslot[1]=='true'){
                  newschedule += '<?php echo get_string('busy','block_parentseve') ?>';
              }else{
                  newschedule += '<input type="radio" name="appointment['+id+']" value="'+timeslot[2]+'" class="termreview_pe_app">';
              }
              newschedule += '</td></tr>';
          }
          newschedule+='</table>';

          //replace the old schedule
          appointment.childNodes[2].innerHTML=newschedule;



        }
    }
    xmlHttp.open("GET","parentseveschedule_ss.php?peid=<?php echo $peid;?>&teacher="+teacherid,true);
    xmlHttp.send(null);



}

function termreview_pe_validate(form){

    if(i==0){
        alert('<?php print_string('noappointments','block_parentseve');?>');
        return false;
    }

    var errors='';
    if(form.parentname.value==''){errors+='<?php print_string('noparentname','block_parentseve');?>'+"\n";}
    if(form.studentname.value==''){errors+='<?php print_string('nostudentname','block_parentseve');?>'+"\n";}

    for(var j=0;j<i;j++){
        var appselected=false;
        var app=document.getElementsByName('appointment['+j+']');
        for(var k=0;k<app.length;k++){
            if(app[k].checked){appselected=true;}
        }
        if (!appselected && !appsdeleted[j]){
            var teacherselect=document.getElementsByName('appointmentteacher['+j+']')[0];
            errors+='<?php print_string('noappointmentwith','block_parentseve');?>'+teacherselect.options[teacherselect.selectedIndex].innerHTML+'\n';
        }
    }
    if(errors!=''){
        alert(errors+"\n"+'<?php print_string('mustcorrect','block_parentseve');?>');
        return false;
    }else{
        return true;
    }
}