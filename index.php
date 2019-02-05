<!--Joseph Ketterer
Jkette01
Web Programming with PHP
Tobi Brodie -->
<?php
require_once 'includes/functions.php';
?>
<!doctype html>
<html lang="en">

<head>
    <title>Module Marks Breakdown</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" type="text/css" href="css/styles.css" />
</head>
    <body>
        <main>
            <h1>Module Marks Breakdown</h1>
            <?php
            /* open file directory and perform checks on directory */
            $handleDir = opendir('data');
            if ($handleDir === false) {
                echo '<p> System error: Unable to open directory</p>';
            }
            else {
                while(false !== ($file = readdir($handleDir))) {
                    if ($file != "." && $file != "..") { # don't add dots which represent directories to array
                        $fileDir1 = array(); # create array
                        array_push($fileDir1, $file); # push into array
                        foreach ($fileDir1 as $key => $value) { # for each loop to loop through files
                            /* I'm checking the file for whitespace as per the specifications provided. The only way to do this without
                            opening the file is to use file_get_contents. This takes up more memory than opening/closing but allows validation
                            before opening. I'm only checking the first 50 charecters */
                            $checkWhiteSpace = (file_get_contents('data/' . $value, FALSE, NULL, 0, 50)); # only check first 50 charecters
                            if ((pathinfo($value, PATHINFO_EXTENSION)) == 'txt'){ # check file is a text file. xml file will be ignored
                                if ((filesize(('data/' . $value)) == 0) || ((ctype_space($checkWhiteSpace)))) { # check file isn't empty & isn't whitespace
                                    echo '<p> File name : '. $value . '</p>'. PHP_EOL; # useful to know which file is empty
                                    echo '<p> This is an empty file and has not been opened </p>'. PHP_EOL;
                                    echo seperator();
                                }
                                else{
                                    echo '<p> File name : '. $value . '</p>' . PHP_EOL;
                                    // open file or report error using string 'data/' and $value to create path to files
                                    $handle = fopen('data/' . htmlentities(trim($value)), 'r');
                                    if ($handle === false) { #error message if you can't open file.
                                        echo '<p>System error. Cannot open file</p>'. PHP_EOL;
                                    } else {
                                        if((checkTitle($handle) > 0) || (checkData($handle) > 0))
                                        {
                                            fclose($handle); #close file if errors above exist
                                            echo "<p>The file contains errors that prevent it being processed</p>";
                                            echo seperator();
                                        }
                                        else{
                                            echo '<h4> Module information </h4>'. PHP_EOL;
                                            echo validateCourseCode(getTitle($handle));
                                            echo validateProgrammeName(getTitle($handle ));
                                            echo validateTutor(getTitle($handle));
                                            echo validateDate(getTitle($handle));
                                            // i'm not printing all the results just the errors with explanation for error as per specifications
                                            echo '<h4> Errors </h4>'. PHP_EOL;
                                            echo getErrors(getData($handle));
                                            echo '<h4> Grades </h4>'. PHP_EOL;
                                            echo  getGrades(getMarks(getData($handle)));
                                            echo '<p> Total errors : ' . countErrors(getData($handle)) .'</p>'. PHP_EOL;
                                            echo '<p> Total students : '. countStudents(getData($handle)) . '</p>'. PHP_EOL;
                                            echo '<h4> Averages </h4>'. PHP_EOL;
                                            echo '<p> Mean : '. round( mmmr(getMarks(getData($handle)))). '</p>'. PHP_EOL;
                                            echo '<p> Mode : ' . round(mmmr( getMarks(getData($handle)), $output = 'mode')) .'</p>'. PHP_EOL;
                                            echo '<p> Range : ' . round(mmmr( getMarks(getData($handle)), $output = 'range')) .'</p>'. PHP_EOL;
                                            echo seperator();
                                            fclose($handle); # close file
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            closedir($handleDir);
            ?>
        </main>
    </body>
</html>
