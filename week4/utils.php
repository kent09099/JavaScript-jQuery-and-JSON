<?php

function validatePos() {
  for($i=1; $i<=9; $i++) {
    if ( ! isset($_POST['year'.$i]) ) continue;
    if ( ! isset($_POST['desc'.$i]) ) continue;

    $year = $_POST['year'.$i];
    $desc = $_POST['desc'.$i];

    if ( strlen($year) == 0 || strlen($desc) == 0 ) {
      return "All fields are required";
    }

    if ( ! is_numeric($year) ) {
      return "Position year must be numeric";
    }
  }
  return true;
}

function validateEdu() {
  for($i=1; $i<=9; $i++) {
    if ( ! isset($_POST['edu_year'.$i]) ) continue;
    if ( ! isset($_POST['edu_school'.$i]) ) continue;

    $year = $_POST['edu_year'.$i];
    $desc = $_POST['edu_school'.$i];

    if ( strlen($year) == 0 || strlen($desc) == 0 ) {
      return "All fields are required";
    }

    if ( ! is_numeric($year) ) {
      return "Education year must be numeric";
    }
  }
  return true;
}


function loadPos($pdo, $profile_id) {
  $stmt = $pdo->prepare("SELECT * FROM Position
    WHERE profile_id = :prof ORDER BY rank");
  $stmt->execute(array( 'prof' =>  $profile_id));
  $positions = array();
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
    $positions[] = $row;
  }
  return $positions;
}


function loadEdu($pdo, $profile_id) {
  $stmt = $pdo->prepare("SELECT * FROM Education join Institution on Education.institution_id =Institution.institution_id
  WHERE profile_id = :prof ORDER BY rank");
  $stmt->execute(array('prof' =>  $profile_id));
  $educations = array();
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
    $educations[] = $row;
  }
  return $educations;
}


function insertPositions($pdo, $profile_id) {
    $rank = 1;
    for($i=1; $i<=9; $i++) {
      if ( ! isset($_POST['year'.$i]) ) continue;
      if ( ! isset($_POST['desc'.$i]) ) continue;

      $year = $_POST['year'.$i];
      $desc = $_POST['desc'.$i];

      $stmt = $pdo->prepare('INSERT INTO Position
        (profile_id, rank, year, description)
        VALUES ( :pid, :rank, :year, :desc)');

      $stmt->execute(array(
      ':pid' => $profile_id,
      ':rank' => $rank,
      ':year' => $year,
      ':desc' => $desc)
      );

      $rank++;
    }
}


function insertEducations($pdo, $profile_id){
    $rank = 1;
    for($i=1; $i<=9; $i++){
      if ( ! isset($_POST['edu_year'.$i]) ) continue;
      if ( ! isset($_POST['edu_school'.$i]) ) continue;

      $year = $_POST['edu_year'.$i];
      $school = $_POST['edu_school'.$i];

      $institution_id = false;
      $stmt = $pdo->prepare('SELECT * from Institution WHERE name = :name');
      $stmt->execute(array(':name' => $school));
      $row  = $stmt->fetch(PDO::FETCH_ASSOC);

      #データベースに学校が存在する場合
      if ($row !== false){
        $institution_id = $row["institution_id"];
      }

      #データベースに学校が存在しない場合
      if ($institution_id === false){
        $stmt = $pdo->prepare("INSERT INTO Institution (name) VALUES (:name)");
        $stmt->execute(array(":name" => $school));
        $institution_id = $pdo->lastInsertId();
      }

      $stmt = $pdo->prepare('INSERT INTO Education
        (profile_id, rank, year, institution_id)
        VALUES ( :pid, :rank, :year, :inst_id)');

      $stmt->execute(array(
      ':pid' => $profile_id,
      ':rank' => $rank,
      ':year' => $year,
      ':inst_id' => $institution_id)
      );

      $rank++;
    }
}

?>
