<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 19/5/2014
 * Time: 9:34 μμ
 */

namespace Schedule\Model;


class Calendar
{

    private $_currMonth;
    private $_currYear;
    private $_currDay;
    private $_monthDays;

    public function __construct($month = "", $year = "", $day = "")
    {
        $this->_monthDays = array('1' => '31', '2' => '28', '3' => '31',
            '4' => '30', '5' => '31', '6' => '30',
            '7' => '31', '8' => '31', '9' => '30',
            '10' => '31', '11' => '30', '12' => '31');
        $this->setDate($month, $day);
        $this->setYear($year);
    }

    private function setDate($month, $day)
    {
        if (!empty($month)) {
            $this->_currMonth = intval($month);
        } else {
            $this->_currMonth = strftime('%m', time());
        }
        if (!empty($day)) {
            $this->_currDay = intval($day);
        } else {
            $this->_currDay = strftime('%d', time());
        }
    }

    private function setYear($year)
    {
        if (!empty($year)) {
            $this->_currYear = intval($year);
        } else {
            $this->_currYear = strftime('%Y', time());
        }
    }

    public function getMonthDays()
    {
        $days = array_search($this->_currMonth, array_keys($this->_monthDays));
        $values = array_values($this->_monthDays);
        if ($days !== false) {
            return $values[$days];
        }
    }

    public function getDay()
    {
        return $this->_currDay;
    }

    public function getMonth($full = true)
    {
        if ($full) {
            return $this->convertMonthNo();
        } else {
            return $this->_currMonth;
        }
    }

    public function getFullDate($greek = true)
    {
        $time = mktime(0, 0, 0, $this->_currMonth, $this->_currDay, $this->_currYear);
        if ($greek) {
            $day = $this->dayToGreek(strftime('%A', $time));
            $month = $this->monthToGreek(strftime('%B', $time));
        } else {
            $day = strftime('%A', $time);
            $month = strftime('%B', $time);
        }
        return $day . ' ' . strftime('%d', $time) . ', ' . $month . ' ' . $this->_currYear;
    }

    public function dayToGreek($day)
    {
        if ($day == 'Monday') {
            $day = 'Δευτέρα';
        } else if ($day == 'Tuesday') {
            $day = 'Τρίτη';
        } else if ($day == 'Wednesday') {
            $day = 'Τετάρτη';
        } else if ($day == 'Thursday') {
            $day = 'Πέμπτη';
        } else if ($day == 'Friday') {
            $day = 'Παρασκευή';
        } else if ($day == 'Saturday') {
            $day = 'Σάββατο';
        } else if ($day == 'Sunday') {
            $day = 'Κυριακή';
        }
        return $day;
    }

    public function monthToGreek($name)
    {
        if ($name == 'January') {
            $name = 'Ιανουάριος';
        } else if ($name == 'February') {
            $name = 'Φεβρουάριος';
        } else if ($name == 'March') {
            $name = 'Μάρτιος';
        } else if ($name == 'April') {
            $name = 'Απρίλιος';
        } else if ($name == 'May') {
            $name = 'Μάιος';
        } else if ($name == 'June') {
            $name = 'Ιούνιος';
        } else if ($name == 'July') {
            $name = 'Ιούλιος';
        } else if ($name == 'August') {
            $name = 'Αύγουστος';
        } else if ($name == 'September') {
            $name = 'Σεπτέμβριος';
        } else if ($name == 'October') {
            $name = 'Οκτώβριος';
        } else if ($name == 'November') {
            $name = 'Νοέμβριος';
        } else if ($name == 'December') {
            $name = 'Δεκέμβριος';
        }
        return $name;
    }

    private function convertMonthNo()
    {
        if ($this->_currMonth == '1') {
            $name = 'January';
        } else if ($this->_currMonth == '2') {
            $name = 'February';
        } else if ($this->_currMonth == '3') {
            $name = 'March';
        } else if ($this->_currMonth == '4') {
            $name = 'April';
        } else if ($this->_currMonth == '5') {
            $name = 'May';
        } else if ($this->_currMonth == '6') {
            $name = 'June';
        } else if ($this->_currMonth == '7') {
            $name = 'July';
        } else if ($this->_currMonth == '8') {
            $name = 'August';
        } else if ($this->_currMonth == '9') {
            $name = 'September';
        } else if ($this->_currMonth == '10') {
            $name = 'October';
        } else if ($this->_currMonth == '11') {
            $name = 'November';
        } else if ($this->_currMonth == '12') {
            $name = 'December';
        }
        return $name;
    }

    public function getYear()
    {
        return $this->_currYear;
    }

    public function nextDay()
    {
        if ($this->_currDay == $this->getMonthDays()) {
            $this->setNextMonth();
            return 1;
        }
        $this->_currDay += 1;
        return $this->_currDay;
    }

    public function previousDay()
    {
        if ($this->_currDay == $this->getMonthDays()) {
            $this->previousMonth();
            return $this->getMonthDays();
        }
        $this->_currDay -= 1;
        return $this->_currDay;
    }

    public function setPreviousMonth()
    {
        if ($this->_currMonth == 1) {
            return 12;
        }
        return $this->_currMonth - 1;
    }

    public function setNextMonth()
    {
        if ($this->_currMonth == 12) {
            return 1;
        }
        return $this->_currMonth + 1;
    }

    public function setNextYear()
    {
        return $this->_currYear + 1;
    }

    public function setPreviousYear()
    {
        return $this->_currYear - 1;
    }

}


?>