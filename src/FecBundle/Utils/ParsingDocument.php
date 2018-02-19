<?php

namespace FecBundle\Utils;


class ParsingDocument
{
    private $path;

    public function __construct($path)
    {
        $this->path = $path;
    }

    /**
     * @param string $startDate
     * @param string $endDate
     * @return mixed
     */
    public function getParsedFileData($startDate = "", $endDate = "")
    {
        $tempResult = $this->ParsingFile();

        //dates for association with transaction amounts
        $dates = $tempResult[0];

        $tempCategoryName = '';
        $tempGroupName = '';
        $tempEntryName = '';

        $order = [".", ",", ":", ";", "'", '"', "$", "@", "#"];
        $replace = "";

        //parsing tempResult to multidimensional array(category->group->entry->transaction)
        foreach ($tempResult as $arr) {

            //create category array
            if (substr($arr[0], -1) === "$") {
                //clear temp group name
                $tempGroupName = '';
                //assign category name
                $tempCategoryName = trim(str_replace($order, $replace, $arr[0]));
                $result[$tempCategoryName] = [];

                //create group array in category array
            } elseif (substr($arr[0], -1) === "@") {

                //clear temp entry name
                $tempEntryName = '';
                //assign group name
                $tempGroupName = trim(str_replace($order, $replace, $arr[0]));
                $result[$tempCategoryName][$tempGroupName] = [];

                //create entry array in group array
            } elseif (substr($arr[0], -1) === "#") {

                $cnt = count($arr);
                foreach ($arr as $key => $value) {

                    $value = trim(str_replace(",", "", $value));

                    if ($key === 0) {

                        //if the category does not have groups, assign the group a category name
                        if (empty($tempGroupName)) {

                            $tempGroupName = $tempCategoryName;

                        }

                        //assign entry name
                        $tempEntryName = trim(str_replace($order, $replace, $value));
                        $result[$tempCategoryName][$tempGroupName][$tempEntryName] = [];

                    } else {

                        //add transactions array (date -> sum)
                        if (!empty($value)) {

                            //date from dates to normal format date
                            $time = $this->dateFormat($dates[$key]);

                            // check date period
                            if (!empty($startDate) && !empty($endDate)) {

                                $testDate = new \DateTime($time);
                                $start = new \DateTime($startDate);
                                $end = new \DateTime($endDate);
                                $period = $this->isDateBetweenDates($testDate, $start, $end);

                            } elseif (!empty($startDate)) {

                                $testDate = new \DateTime($time);
                                $start = new \DateTime($startDate);
                                $end = new \DateTime();
                                $period = $this->isDateBetweenDates($testDate, $start, $end);

                            } elseif (!empty($endDate)) {

                                $testDate = new \DateTime($time);
                                $start = new \DateTime("1970-01-01 00:00:00");
                                $end = new \DateTime($endDate);
                                $period = $this->isDateBetweenDates($testDate, $start, $end);

                            } else {

                                $period = '';
                            }

                            if (!empty($period)) {

                                $result[$tempCategoryName][$tempGroupName][$tempEntryName][$time] = $value;

                            } elseif ($period === 'all') {

                                $result[$tempCategoryName][$tempGroupName][$tempEntryName][$time] = $value;

                            }
                        }
                    }
                }
            }
        }
        return $result;
    }

    /**
     * @param $data
     * @return array|string
     */
    private function dateFormat($data)
    {
        $time = explode('/', $data);
        if ($time[2] >= 00 && $time[2] <= 70) {
            $time = '20' . $time[2] . '-' . $time[1] . '-' . $time[0] . ' 00:00:00';
        } else {
            $time = '19' . $time[2] . '-' . $time[1] . '-' . $time[0] . ' 00:00:00';
        }

        return  $time;
    }

    /**
     * @return array|bool
     */
    private function ParsingFile()
    {
        /*open file*/
        if (($document = fopen($this->path, "r")) !== false) {
            /*read file*/
            while (($data = fgetcsv($document, ";")) !== false) {
                // write result to array
                $tempResult[] = $data;
            }
            fclose($document);
        } else {
            return false;
        }
        return $tempResult;
    }


    /**
     * @param \DateTime $date Date that is to be checked if it falls between $startDate and $endDate
     * @param \DateTime $startDate Date should be after this date to return true
     * @param \DateTime $endDate Date should be before this date to return true
     * @return bool
     */
    private function isDateBetweenDates(\DateTime $date, \DateTime $startDate, \DateTime $endDate)
    {
        if ($startDate > $endDate){
            return false;
        } else {
            return $date >= $startDate && $date <= $endDate;
        }
    }
}