<?php
require_once ($CFG->libdir.'/formslib.php');
class parentseve_form extends moodleform {
        function definition() {
        $mform    =& $this->_form;
        $mform->addElement('hidden','id');
        $mform->addElement('hidden','cmid');
        $mform->addElement('hidden','reviewid');
        $mform->addElement('date_time_selector', 'timestart', get_string('timestart', 'block_parentseve'));
        $mform->addElement('date_time_selector', 'timeend', get_string('timeend', 'block_parentseve'));
        $mform->addElement('text','appointmentlength',get_string('appointmentlength', 'block_parentseve')); // will have to take this in minutes until figure out duration element type (moodle 2.0)
        $mform->addElement('htmleditor', 'info', get_string('parentseveinfo', 'block_parentseve'),'rows="10" cols="25"');

        $mform->addElement('hidden', 'teachers', null, array('id' => 'id_teachers'));

    $this->add_action_buttons(false);
    }
}

function parentseve_teacher_form($selectedusers, $unselectedusers, $searchtext = '') {
    global $CFG, $THEME, $id;
    $rekey = $selectedusers;
    $selectedusers = array();
    foreach ($rekey as $user) {
    	$selectedusers[$user->id] = $user;
    }
    unset($rekey);
    echo '<form method="post" action="'.$CFG->wwwroot.'/blocks/parentseve/edit.php';
        if (!empty($id)) {
            echo '?id='.$id;
        }
    echo '"><table summary="" style="margin-left:auto;margin-right:auto" border="0" cellpadding="5" cellspacing="0">
            <tr>
                <td valign="top">
                <label for="removeselect">'.get_string('parentseveteachers', 'block_parentseve').'</label>
          <br />
          <select name="removeselect[]" size="20" id="removeselect" multiple="multiple" >';


    $i = 0;
    foreach ($selectedusers as $selecteduser) {
        $fullname = fullname($selecteduser);
        echo '<option value="'.$selecteduser->id.'">'.$fullname."</option>\n";
        $i++;
    }

    if ($i == 0) {
        echo '<option/>'; // empty select breaks xhtml strict
    }

      echo '</select></td>
      <td valign="top">
        <br />';
        check_theme_arrows();
        echo '<p class="arrow_button">';
        /*echo '<script type="text/javascript">
                document.write(\'<input name="add" id="add" type="button" value="'. $THEME->larrow.'&nbsp;'.get_string('add').'" title="'.get_string('add').'" onclick="parentseve_addteachers()" /><br />\');
                document.write(\'<input name="remove" id="remove" type="button" value="'.get_string('remove').'&nbsp;'.$THEME->rarrow.'" title="'.get_string('remove').'" onclick="parentseve_removeteachers()"/>\');
            </script>
            <noscript>';*/
            echo'<input name="add" id="add" type="submit" value="'. $THEME->larrow.'&nbsp;'.get_string('add').'" title="'.get_string('add').'" /><br />
                <input name="remove" id="remove" type="submit" value="'.get_string('remove').'&nbsp;'.$THEME->rarrow.'" title="'.get_string('remove').'" />';
        //'</noscript>'
        echo '</p>
      </td>
      <td valign="top">
          <select name="addselect[]" size="20" id="addselect" multiple="multiple" >';

        $i = 0;
        if (!empty($searchtext)) {
           $unselectedusers = array_filter($unselectedusers, 'parentseve_search_filter');
        }
        foreach ($unselectedusers as $unselecteduser) {
                $fullname = fullname($unselecteduser);
                echo '<option value="'.$unselecteduser->id.'">'.$fullname."</option>\n";
                $i++;
        }
        if ($i==0) {
            echo '<option />'; // empty select breaks xhtml strict
        }

        echo '</select>
         <br />
         <label for="searchtext" class="accesshide">'.get_string('search').'</label>
         <input type="text" name="searchtext" id="searchtext" size="30" value="'.$searchtext.'" />         
         <input name="search" id="search" type="submit" value="'.get_string('search').'" title="'.get_string('search').'" />
         <input name="teachers" type="hidden" value="'.implode(',', array_keys($selectedusers)).'" />';
          if (!empty($searchtext)) {
              echo '<input name="showall" id="showall" type="submit" value="'.get_string('showall').'" />'."\n";
          }

       echo '</td>
    </tr>
  </table>
  </form>';
  
}

?>