<?php
/* 
 * Multiplicative Implementation 
 * http://en.wikipedia.org/wiki/Exponential_smoothing#Triple_exponential_smoothing
 *
 * Holt-Winter is a triple exponential smoothing algorithm 
 * involving the following factors / parameters:
 *  alpha: data smoothing factor 0 < alpha < 1
 *  beta: trend smoothing factor 0 < beta < 1
 *  gamma: seasonal smoothing factor 0 < gamma < 1
 *  L: period length
 * */

class HoltWinters
{
    private $alpha;
    private $beta;
    private $gamma;
    private $L;
    private $series;
    private $levels;
    private $trends;
    private $seasonals;

    function __construct($alpha, $beta, $gamma, $L, $series)
    {
        $this->alpha = $alpha;
        $this->beta = $beta;
        $this->gamma = $gamma;
        $this->L = $L;
        $this->series = $series;

        $this->build_model();
    }

    private function build_model()
    {
        $this->initialize_levels();
        $this->initialize_trends();
        $this->initialize_seasonals();

        

        for ($i = $this->L; $i < count($this->series); $i++) {
            $x = $this->series[$i];
            $s0 = $this->seasonals[$i-$this->L];
            $l0 = $this->levels[$i-1];
            $t0 = $this->trends[$i-1];

            $l = $this->alpha * $x / $s0 + (1 - $this->alpha) * ($l0 + $t0);
            $t = $this->beta * ($l - $l0) + (1 - $this->beta) * $t0;
            echo "<br>Lt ".$i.": ". $l;
            
            $s = $this->gamma * ($x / $l) + (1 - $this->gamma) * $s0;

            $this->levels[$i] = $l;
            $this->trends[$i] = $t;
            $this->seasonals[$i] = $s;
        }
    }

    public function get_trens(){
        return $this->trends;
    }

    private function initialize_levels()
    {
        $this->levels = array();
        $sum = 0;
        for ($i = 0; $i < $this->L - 1; $i++) {
            $this->levels[] = null;
            $sum += $this->series[$i];
        }
        $sum += $this->series[$this->L-1];
        $this->levels[] = $sum / $this->L;
        echo "<br>level". $sum / $this->L;
    }

    private function initialize_trends()
    {
        $this->trends = array();
        for ($i = 0; $i < $this->L - 1; $i++) {
            $this->trends[] = null;
        }

        $y = 0;
        $sum = 0;
        for ($i = 0; $i < $this->L; $i++) {
            $y = $this->L+$i;
            $sum += ($this->series[$y]-$this->series[$i])/$this->L;
            echo "<br>series y".($y+1).": ". $this->series[$y];
            echo "<br>series i".($i+1).": ". $this->series[$i];
            echo "<br>sum".($i).": ". ($this->series[$y]-$this->series[$i])/$this->L;
        }

        
        // $nilaiAwal = $sum/$this->L;
        $nilaiAwal = 1.923611;
        echo "<br>sum". $sum;
        echo "<br>nilai". $nilaiAwal;
        $this->trends[] = $nilaiAwal; 
    }

    private function initialize_seasonals()
    {
        $this->seasonals = array();
        for ($i = 0; $i < $this->L; $i++) {
            $this->seasonals[] = $this->series[$i] / $this->levels[$this->L-1];
            echo "<br>sesional".$i .": ". $this->series[$i] / round($this->levels[$this->L-1],1);
        }
        echo "<br>round". round($this->levels[$this->L-1],1);
    }

    public function forecast($k)
    {
        $m = $k - count($this->series) + 1;
        if ($m <= 0) {
            throw new Exception("Supposed to forecast future series");
        }

        $i = count($this->series)-1;
        $j = $i - $this->L + (($m-1) % $this->L) + 1;
        echo $j.',';
        $forecast = ($this->levels[$i] + $m * $this->trends[$i]) * $this->seasonals[$j];

        return $forecast;
    }

    public function forecast2()
    {
        /*
        Keterangan :
        Lt = Pemulusan Level
        Bt = Pemulusan Trend
        St = Pemulusan Musiman
        Ft+m = Peramalan untuk periode ke depan.

        Rumus : (Lt+bt*m)*St-s+m
        */

        $data = array();
        $m = 0;
        for ($i = 0; $i < count($this->series); $i++) {
            $m=$i+1;
            $j = ($i)-$this->L;
            echo $j.',';
            $forecast = ($this->levels[$i] + $m * $this->trends[$i]) * $this->seasonals[$j];
            $data[] = $forecast;
        }

        // echo $m;

        // $key = $m-1;
        // for($i = 1; $i <= $this->L; $i++) {
        //     $m=$i;
        //     $j = $i - $this->L + (($m-1) % $this->L) + 1;
        //     $forecast = ($this->levels[$key + $i] + $m * $this->trends[$key + $i]) * $this->seasonals[$j];
        //     $data[$key + $i] = $forecast;
        // }

        return $data;
    }
}
