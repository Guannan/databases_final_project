<?php
    /**
     * Contains constants used by other pages
     */


    /**
     * For accessing arbitrary pairs of relations. Each element is of the form:
     *    English Name => array(Table, Select Term, Name of row in returned table, (Optional) Attribute to group on)
     */
    $fields = array("Num Connections" => array("User", "User.NumConnection", "NumConnection"),
        "Age" => array("User", "User.Age", "Age"),
        "Num Skills" => array("Has_skill", "COUNT(Has_skill.SkillID)", "NumSkills", "UserID"),
        "Num Languages" => array("Knows_language", "COUNT(Knows_language.LanguageID)", "NumLanguages", "UserID"),
        "Num Jobs" => array("Experience", "COUNT(Experience.EmployerID)", "NumJobs", "UserID")
      );

?>

