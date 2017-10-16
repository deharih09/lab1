<?php

class DegreeModel extends Model {

    public function saveRequirement() {
        $data = json_decode(file_get_contents('php://input'));
        $category = $data->category;
        $category_id = $category . '_id';
        $tmp_data = new stdClass();
        // Assign a unique requirement id if it is a new requirement
        $req_id = -1;
        if (isset($data->req_id))
            $req_id = intval($data->req_id);
        if ($req_id === -1)
            $req_id = $this->getRequirementID($category, $category_id);

        /* Course list is in the format 'subject nnn, subject nnn, ...' 
         * Need to convert course list to the format 'courseid, courseid, ...'
         */
        $c = trim($data->courses);
        if (intVal($c) === -1) {
            $courseList = -1;
        } else {
            if (stripos($c, ',') === false) {
                // No change in the value or incorrect format. Do not update course list

                $info = array(trim($c));
            } else {
                $info = explode(',', $c);
            }
            $courseList = $this->getCourseList($info);
        }
        // If course list is invalid send an appropriate message
        if (!$courseList) {
            $tmp_data->mode = -2;
            return $tmp_data;
        }

        // Define Modal information
        $modal_info = $this->getModalInfo($data->state);
        return $this->saveData($category, $req_id, $data, $modal_info, $category_id, $courseList);
    }

    public function saveData($category, $req_id, $data, $modal_info, $category_id, $courseList) {

        $values = array('req_id' => $req_id, $category . '_id' => $data->category_id, 'courses' => $courseList, 'label' => $data->label,
            'notes' => $data->notes, 'min_credits' => $data->min_credits, 'choices' => $data->choices,
            'max_credits' => $data->max_credits, 'num_courses' => $data->num_courses, 'type' => $data->type, 'state' => $data->state);
        $table = $category . "_requirements";
        if (trim($data->id) == -1) {
            $result = $this->insertRecord($table, $values);
        } else
            $result = $this->updateRecord($table, $values, ' id=:id ', array('id' => $data->id));
        $tmp_data = new stdClass();
        $tmp_data->mode = ($result) ? 1 : -1;

        return $tmp_data;
    }

    public function getCourseList($info) {
        $str = '';

        for ($i = 0; $i < count($info); $i++) {
            if (isset($info[$i]) && ($info[$i] != '')) {
                $line = explode(' ', trim($info[$i]));
                $subject = trim($line[0]);
                $id = trim($line[1]);


                if (isset($id) && ($id != '')) {
                    $course_info = $this->getCourseid($subject, $id);
                    if (is_object($course_info))
                        $str .= $course_info->id . ", ";
                    else
                        return false;
                }
            }
        }
        $str = substr($str, 0, -2);
        return $str;
    }

    public function getCourseid($subject, $number) {

        $course_list = array();
        $sql = "SELECT c.id
FROM course AS c
WHERE 
c.subject = :subject AND number = :number
ORDER BY c.id";
        $stm = $this->_db->prepare($sql);
        //$sub = '%' . $subject . '%';
        $items = array(':subject' => $subject, ':number' => $number);
        //$stm->bindParam(1, $subject);
        $stm->execute($items);

        return $stm->fetch();
    }

    public function getModalInfo($state) {
        $controller = '';
        $template = '';
        $method = '';
        /* if ($state == 3) {
          switch ((int) $substate) {
          case 0:
          $controller = "RadioModalController";
          $template = "selectRadioGroup.html";
          $method = "selectOneElective";
          break;
          case 1:
          $controller = "SelectManyModalController";
          $template = "selectCheckboxes.html";
          $method = "selectManyElectives";
          break;
          }
          } */
        return array('controller' => $controller, 'template' => $template, 'method' => $method);
    }

    public function getRequirementID($category, $category_id) {
        $sql1 = 'SELECT req_id FROM `' . $category . '_requirements` WHERE ' . $category_id . ' = :id order by req_id DESC limit 1';
        $stm1 = $this->_db->prepare($sql1);
        $stm1->execute(array(':id' => $category_id));
        $req_id = 0;
        $last_index = $stm1->rowCount();
        if ($last_index > 0) {
            $result = $stm1->fetch();
            $req_id = (int) $result->req_id + 1;
        }
        return $req_id;
    }

}
