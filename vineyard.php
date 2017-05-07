<?php

ini_set('memory_limit', '4095M');
set_time_limit(0);

/**
 * Class Vineyard contains  functions which iterate the file "person_wine_3.txt" and generate the final list of all wines allotement and  generate the  file with desired result.
 *
 * output in the TSV File named as wine_alloted_list.txt(should be write permission to the file)
 * 
 * use "php vineyard.php" command to get the output
 * 
 */
class Vineyard {

    /**
     * Class Vineyard
     */
    public $allWineList;
    public $personWineWishlist;
    public $wineFinalAllotmentList;
    public $totalWineBottlesSold;

    /**
     * Constructor function 
     *
     * declaring the data types of the variables.
     */
    function __construct() {
        $this->allWineList = array();
        $this->personWineWishlist = array();
        $this->wineFinalAllotmentList = array();
        $this->totalWineBottlesSold = 0;
    }

    /**
     * function generateWineList
     *
     * Function to create the list of all the wines in Vineyard and also create list of wishlist of wines for each person(upto 10 wines).
     *
     */
    public function generateWineList($wineWishlistFileName) {
        $fp = fopen($wineWishlistFileName, "r");
        while (!feof($fp)) {
            $line = fgets($fp, 2048);
            $data = str_getcsv($line, "\t");
            if ($data[0] != '') {
                $personName = trim($data[0]);
                $wineCode = trim($data[1]);
                if (!array_key_exists($wineCode, $this->personWineWishlist)) {
                    $this->personWineWishlist[$wineCode] = [];
                }
                $this->personWineWishlist[$wineCode][] = $personName;
                $this->allWineList[] = $wineCode;
            }
        }
        fclose($fp);
        $this->allWineList = array_unique($this->allWineList);
    }

    /**
     * function generateWineAllotmentList
     *
     * Function to generate the final allotment list of wines to persons .
     *
     */
    public function generateWineAllotmentList() {
        foreach ($this->allWineList as $key => $wineCode) {
            foreach ($this->personWineWishlist[$wineCode] as $keys => $personCode) {
                if (!array_key_exists($personCode, $this->wineFinalAllotmentList)) {
                    $this->wineFinalAllotmentList[$personCode][] = $wineCode;
                    $this->totalWineBottlesSold++;
                    break;
                } else {
                    if (count($this->wineFinalAllotmentList[$personCode]) < 3) {
                        $this->wineFinalAllotmentList[$personCode][] = $wineCode;
                        $this->totalWineBottlesSold++;
                        break;
                    }
                }
            }
        }
    }

    /**
     * function outputWineAllotmentList
     *
     * Function used to generate the required tsv file with desired result .
     *
     */
    public function outputWineAllotmentList($exportFilename) {
        $fh = fopen($exportFilename, "w");
        $heading = "Total number of wines bottles sold in aggregate is " . $this->totalWineBottlesSold . " by vineyard. Wine allotment list is geberated in wine_alloted_list.txt";
        fwrite($fh, $heading);
        foreach ($this->wineFinalAllotmentList as $personCode => $winelist) {
            foreach ($this->wineFinalAllotmentList[$personCode] as $key => $wineCode) {
                fwrite($fh, "\n" . $personCode . " \t " . $wineCode);
            }
        }
        fclose($fh);
        echo $heading . '     ';
    }

}

$vineyard = new Vineyard();
$vineyard->generateWineList("person_wine_3.txt");
$vineyard->generateWineAllotmentList();
$vineyard->outputWineAllotmentList('wine_alloted_list.txt');
?>
