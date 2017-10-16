<?php

include('../.config.php');
try {
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME;
    // establish a connection
    $db = new PDO($dsn, DB_USER, DB_PASS);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // set default fetch mode
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
} catch (PDOException $e) {
    die();
}


$data = json_decode(file_get_contents('php://input'));
$category = $data->category;
$category_id = $category . '_id';

$sql1 = 'SELECT req_id FROM `' . $category . '_requirements` WHERE ' . $category_id . ' = :id order by req_id DESC limit 1';
$stm1 = $db->prepare($sql1);
$stm1->execute(array(':id' => $data->{$category_id}));
$req_id = 0;
$last_index = $stm1->rowCount();
if ($last_index > 0) {
    $result = $stm1->fetch();
    $req_id = (int) $result->req_id + 1;
}


$c = $data->courses;
if (stripos($c, ':') === false) {
    // No change in the value or incorrect format. Do not update course list
    //$mode = 1;
    $courseList = $c;
} else {
    $info = explode(';', $c);
    $courseList = getCourseList($info);
    //$mode = 2;
}
$modal_info = getModalInfo($data->state, $data->sub_state);


$sql = "INSERT INTO  {$category}_requirements (label,  notes, min_credits, max_credits,  state, req_id, courses, {$category_id}, "
        . "num_courses, type, choices, controller, template, method) ";
$sql .= " VALUES (:label, :notes,  :min_credits, :max_credits,  :state, :req_id, :courses, :category_id, "
        . ":num_courses, :type , :choices, '{$modal_info['controller']}', '{$modal_info['template']}', '{$modal_info['method']}')";
$values = array(':req_id' => $req_id, ':category_id' => $data->{$category_id}, ':courses' => $courseList, ':label' => $data->label,
    ':notes' => $data->notes, ':min_credits' => $data->min_credits, ':choices' => $data->choices,
    ':max_credits' => $data->max_credits, ':num_courses' => $data->num_courses, ':type' => $data->type, ':state' => $data->state);

$stm = $db->prepare($sql);
$result = $stm->execute($values);
if ($result)
    echo 1;
else
    echo -1;






function getCourseList($info) {
    $str = '';


    for ($i = 0; $i < count($info); $i++) {
        if (isset($info[$i]) && ($info[$i] != '')) {
            $line = explode(':', $info[$i]);
            $subject = trim($line[0]);
            $ids = explode(',', $line[1]);

            for ($j = 0; $j < count($ids); $j++) {
                if (isset($ids[$j]) && ($ids[$j] != '')) {
                    //$course_info = getCourseid($subject, trim($ids[$j]));
                    //$str .= $course_info->id.", ";
                    $str .= $subject . trim($ids[$j]) . ", ";
                }
            }
        }
        $str = substr($str, 0, -2);
        return $str;
    }
}

function getCourseid($subject, $number) {
    global $db;
    echo "Number: " . $number . '<br/>';
    $course_list = array();
    $sql = "SELECT c.id
FROM course AS c
WHERE c.institution =  'uwcolleges'
AND c.subject like :subject AND number = :number
ORDER BY c.id";
    $stm = $db->prepare($sql);
    $sub = '%' . $subject . '%';
    $items = array(':subject' => $sub, ':number' => $number);
    //$stm->bindParam(1, $subject);
    $stm->execute($items);

    return $stm->fetch();
}

function getModalInfo($state, $substate) {
    $controller = '';
    $template = '';
    $method = '';
    if ($state == 3) {
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
    }
    return array('controller' => $controller, 'template' => $template, 'method' => $method);
}
