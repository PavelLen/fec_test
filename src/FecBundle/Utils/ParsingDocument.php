<?php

namespace FecBundle\Utils;


class ParsingDocument
{
    private $path;

    /**
     * ParsingDocument constructor.
     * @param $path
     */
    public function __construct($path)
    {
        $this->path = $path;
    }

    /**
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @return mixed
     */
    public function getParsedFileData(\DateTime $startDate, \DateTime $endDate)
    {
        $tempResult = $this->ParsingFile();

        /**
         * dates for association with transaction amounts
         */
        $dates = $tempResult[0];

        $tempCategoryName = '';
        $tempGroupName = '';
        $tempEntryName = '';

        $order = [".", ",", ":", ";", "'", '"', "$", "@", "#"];
        $replace = "";

        /**
         * parsing tempResult to multidimensional array(category->group->entry->transaction)
         */
        foreach ($tempResult as $arr) {

            //create category array
            if (substr($arr[0], -1) === "$") {
                //clear temp group name
                $tempGroupName = '';
                //assign category name
                $tempCategoryName = trim(str_replace($order, $replace, $arr[0]));
                $result[$tempCategoryName] = [];

                /**
                 * create group array in category array
                 */
            } elseif (substr($arr[0], -1) === "@") {

                /**
                 * clear temp entry name
                 */
                $tempEntryName = '';
                /**
                 * assign group name
                 */
                $tempGroupName = trim(str_replace($order, $replace, $arr[0]));
                $result[$tempCategoryName][$tempGroupName] = [];

                /**
                 * create entry array in group array
                 */
            } elseif (substr($arr[0], -1) === "#") {
                foreach ($arr as $key => $value) {

                    $value = trim(str_replace(",", "", $value));

                    if ($key === 0) {

                        /**
                         * if the category does not have groups, assign the group a category name
                         */
                        if (empty($tempGroupName)) {
                            $tempGroupName = $tempCategoryName;
                        }
                        /**
                         * assign entry name
                         */
                        $tempEntryName = trim(str_replace($order, $replace, $value));
                        $result[$tempCategoryName][$tempGroupName][$tempEntryName] = [];

                    } else {

                        /**
                         * add transactions array (date -> sum)
                         */
                        if (!empty($value)) {

                            /**
                             * date from dates to normal format date
                             */
                            $time = $this->dateFormat($dates[$key]);

                            /**
                             * check date period
                             */
                            $testDate = new \DateTime($time);
                            $period = $this->isDateBetweenDates($testDate, $startDate, $endDate);

                            /**
                             * add transaction
                             */
                            if ($period) {
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
        if (($document = fopen($this->path, "r")) !== false) {
            while (($data = fgetcsv($document, ";")) !== false) {
                /**
                 * write result to array
                 */
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