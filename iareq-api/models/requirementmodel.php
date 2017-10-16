<?php

class RequirementModel extends Model {

    public function getCategoryList($subject) {
        $sql = "SELECT id, name FROM $subject";
        $this->_sql = $sql;
        return $this->getAll();
    }

    public function getRequirements($category, $category_id) {
        $table = $category . "_requirements";
        $requirements_list = $this->getCategoryInfo($table, $category, $category_id);
        $requirements = array();
        for ($i = 0; $i < count($requirements_list); $i++) {
            $requirements[] = $this->createSubCatagoryList($requirements_list[$i]);
        }
        return $requirements;
    }

    public function getCategoryInfo($table, $category, $category_id) {
        $sql = "SELECT  id, major_id, req_id ,  label, courses, num_courses, units, state, template, controller, method, min_credits, max_credits, model, notes, type  "
                . "FROM " . $table . " where " . $category . "_id = :id order by req_id";
        $this->_sql = $sql;
        $data = array(':id' => $category_id);
        return $this->getAll($data);
    }

    public function createSubCatagoryList($list) {
        if (isset($list->courses)) {
            $req_courses = $this->convertToArray($list->courses, ',');
            $course_list = $this->prepareReqCourseList($req_courses);
        } else
            $course_list = array();
        $requirements = array('id' => $list->id, 'req_id' => $list->req_id, 'label' => $list->label, 'courses' => $course_list, 'num_courses' => $list->num_courses, 'units' => $list->units,
            'state' => $list->state, 'template' => $list->template, 'controller' => $list->controller, 'method' => $list->method, 'model' => $list->model, 'min_credits' => $list->min_credits,
            'notes' => $list->notes, 'max_credits' => $list->max_credits, 'type' => $list->type);
        return $requirements;
    }

    public function prepareReqCourseList($req_courses) {
        $tmp_list = '';
        if (count($req_courses) > 0) {
            if (intVal($req_courses[0]) === -1) {
                $tmp_list = '-1';
            } else {
                for ($i = 0; $i < count($req_courses); $i++) {
                    $course_info = $this->getCourseDetails($req_courses[$i]);
                    $tmp_list .= $course_info['subject'] . " " . $course_info['number'] . ", ";
                }
                $tmp_list = substr($tmp_list, 0, -2);
            }
        }
        if (trim($tmp_list) == ',')
            $tmp_str = '-1';
        return $tmp_list;
    }

    public function createCell($cell_style, $cell_content) {
        return "<div class='" . $cell_style . "' >" . $cell_content . " </div>\n";
    }

    public function getCourseDetails($index) {
        $sql = "SELECT id, subject,  number, title, maxcredits, postfixPrereq, prereq, description, ge_category FROM course WHERE id = '$index'";
        $this->_sql = $sql;
        $stm = $this->_db->prepare($this->_sql);
        $stm->execute();
        //      if ($stm->rowCount() >0)
        return $stm->fetch(PDO::FETCH_ASSOC);
        //    else
        //   return -3;
        //return $this->getOne();
//return(array('id'=>1, 'subject'=>'compsci', 'number'=>120, 'maxcredits'=>3));
    }

    public function convertToArray($str, $find) {
        if (strpos($str, $find)) {
            $array_list = explode($find, $str);
        } else {
            $array_list = array(trim($str));
        }
        return $array_list;
    }

    public function saveRequirement() {
        $data = json_decode(file_get_contents('php://input'));
        $c = $data->courses;
        if (stripos($c, ':') === false) {
            // No change in the value or incorrect format. Do not update course list
            $courseList = -1;
        } else {
            $info = explode(';', $c);
            $courseList = $this->getCourseList($info);
        }

        if ($data->id == -1) {

            $values = array('courses' => $courseList, 'major_id' => $data->major_id, 'req_id' => $data->req_id, 'label' => $data->label, 'notes' => $data->notes, 'min_credits' => $data->min_credits,
                'max_credits' => $data->max_credits, 'units' => $data->units, 'state' => $data->state);
            $this->insertRecord('major_requirements', $values);
        } else {

            $changes = array('courses' => $courseList, 'label' => $data->label, 'notes' => $data->notes,
                'min_credits' => $data->min_credits,
                'max_credits' => $data->max_credits, 'units' => $data->units, 'state' => $data->state);
            $condition = " id = :id ";
            $values = array('id' => $data->id);
            $this->updateRecord('major_requirements', $changes, $condition, $values);
        }
        return 1;
    }

    public function getCourseList($info) {
        $str = '';


        for ($i = 0; $i < count($info); $i++) {
            if (isset($info[$i]) && ($info[$i] != '')) {
                $line = explode(':', $info[$i]);
                $subject = trim($line[0]);
                $ids = explode(',', $line[1]);
                $sql = "SELECT c.id
                    FROM course AS c
                    WHERE c.institution =  'uww'
                    AND c.subject like :subject AND number = :number
                    ORDER BY c.id";
                $stm = $this->_setSql($sql);
                for ($j = 0; $j < count($ids); $j++) {
                    if (isset($ids[$j]) && ($ids[$j] != '')) {
                        //$course_info = getCourseid($subject, trim($ids[$j]));
                        $sub = '%' . $subject . '%';
                        $items = array(':subject' => $sub, ':number' => trim($ids[$j]));
                        //$stm->bindParam(1, $subject);
                        //$->execute($items);

                        $course_info = $this->getOne($items);
                        if (isset($course_info->id))
                            $str .= $course_info->id . ", ";
                        else
                            $str .= " -1 , ";
                    }
                }
            }
            $str = substr($str, 0, -2);
            return $str;
        }
    }

    public function getCourseid($subject, $number) {
        global $db;

        $course_list = array();
        $sql = "SELECT c.id
FROM course AS c
WHERE c.institution =  'uww'
AND c.subject like :subject AND number = :number
ORDER BY c.id";
        $stm = $db->prepare($sql);
        $sub = '%' . $subject . '%';
        $items = array(':subject' => $sub, ':number' => $number);
        //$stm->bindParam(1, $subject);
        $stm->execute($items);

        return $stm->fetch();
    }

}
