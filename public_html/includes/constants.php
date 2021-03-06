<?php
    /**
     * Contains constants used by other pages
     */

    $tables = array("User", "Education", "Degree", "University", "Experience", "Employer", "Member_of", 
                    "Groups", "Has_skill", "Skill", "Knows_language", "Languages");
    
    // Attributes on which we can join these tables
    $cross_refs = array(array(1=>"User.UserID=Education.UserID", 4=>"User.UserID=Experience.UserID", 6=>"User.UserID=Member_of.UserID",
                              8=>"User.UserID=Has_skill.UserID", 10=>"User.UserID=Knows_language.UserID"),
                        array(0=>"Education.UserID=User.UserID", 2=>"Education.DegreeID=Degree.DegreeID", 3=>"Education.UniversityID=University.UniversityID"),
                        array(1=>"Degree.DegreeID=Education.DegreeID"),
                        array(1=>"University.UniversityID=Education.UniversityID"),
                        array(0=>"Experience.UserID=User.UserID", 5=>"Experience.EmployerID=Employer.EmployerID"),
                        array(4=>"Employer.EmployerID=Experience.EmployerID"),
                        array(0=>"Member_of.UserID=User.UserID", 7=>"Member_of.GroupID=Groups.GroupID"),
                        array(6=>"Groups.GroupID=Member_of.GroupID"),
                        array(0=>"Has_skill.UserID=User.UserID", 9=>"Has_skill.SkillID=Skill.SkillID"),
                        array(8=>"Skill.SkillID=Has_skill.SkillID"),
                        array(0=>"Knows_language.UserID=User.UserID", 11=>"Knows_language.LanguageID=Languages.LanguageID"),
                        array(10=>"Languages.LanguageID=Knows_language.LanguageID"));


    /**
     * For accessing arbitrary pairs of relations. Each element is of the form:
     *    English Name => array(Select term, List of Tables, WHERE term, Attribute to group on)
     */
    $continuous_vars = array("Num Connections" => array("User.NumConnection", array(0), "", ""),
        "Age" => array("User.Age", array(0), "", ""),
        "Num Skills" => array("COUNT(Has_skill.SkillID)", array(8), "", "Has_skill.UserID"),
        "Num Languages" => array("COUNT(Knows_language.LanguageID)", array(10), "", "Knows_language.UserID"),
        "Num Jobs" => array("COUNT(Experience.EmployerID)", array(4), "", "Experience.UserID")
      );

    $categorical_vars = array("Industry" => array("User.Industry", array(0), "", ""),
        "University" => array("University.UniversityName", array(1,3), "", ""),
        "Degree" => array("Degree.DegreeName", array(1,2), "", ""),
        "Skill" => array("Skill.SkillName", array(8,9), "", ""),
        "Language" => array("Languages.LanguageName", array(10,11), "", "")
    );

    // Attributes to filter on
    // Format: Name => array(type (0=numeric, 1=categorical), Table ID, Attribute)
    $filters = array("Age" => array(0, array(0), "User.Age", ""),
        "NumConnections"=>array(0, array(0), "User.NumConnection", ""),
//        "NumSkills"=>array(0, array(8), "COUNT(Has_skill.SkillID)", " GROUP BY UserID"),
//        "NumLanguages"=>array(0, array(10), "COUNT(Knows_language.LanguageID)", " GROUP BY UserID"),
        "Industry"=>array(1, array(0), "User.Industry", ""),
        "University"=>array(1, array(3,1), "University.UniversityName", ""),
        "Degree"=>array(1, array(2,1), "Degree.DegreeName", ""),
        "Skill"=>array(1, array(9,8), "Skill.SkillName", ""),
        "Language"=>array(1, array(11,10), "Languages.LanguageName", "")
    );
?>

