import React, {Component} from 'react'
class contributor extends Component {
    state = {  }
    logValue = () => {
        alert('moi')
    }
    render() {
        
        return (
            <div class="row">        

        <div class="col-md-8">
            <form name="assistance" method="post" id="assistance_form">
            <fieldset class="form-group"><legend style="font-weight: bold;" class="col-form-label required">Fecha de creación</legend><div id="assistance_createdAt" class="form-inline"><div class="sr-only"><label class="required" for="assistance_createdAt_date_year">Year</label><label class="required" for="assistance_createdAt_date_month">Month</label><label class="required" for="assistance_createdAt_date_day">Day</label><label class="required" for="assistance_createdAt_time_hour">Hour</label><label class="required" for="assistance_createdAt_time_minute">Minute</label></div><div class="sr-only">
                <label class="required" for="assistance_createdAt_date_year">Year</label>
                <label class="required" for="assistance_createdAt_date_month">Month</label>
                <label class="required" for="assistance_createdAt_date_day">Day</label>
            </div>
            <select id="assistance_createdAt_date_day" name="assistance[createdAt][date][day]" class="form-control"><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option><option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14">14</option><option value="15">15</option><option value="16">16</option><option value="17">17</option><option value="18">18</option><option value="19">19</option><option value="20">20</option><option value="21">21</option><option value="22">22</option><option value="23">23</option><option value="24">24</option><option value="25">25</option><option value="26">26</option><option value="27">27</option><option value="28">28</option><option value="29">29</option><option value="30">30</option><option value="31">31</option></select><select id="assistance_createdAt_date_month" name="assistance[createdAt][date][month]" class="form-control"><option value="1">ene</option><option value="2">feb</option><option value="3">mar</option><option value="4">abr</option><option value="5">may</option><option value="6">jun</option><option value="7">jul</option><option value="8">ago</option><option value="9">sept</option><option value="10">oct</option><option value="11">nov</option><option value="12">dic</option></select><select id="assistance_createdAt_date_year" name="assistance[createdAt][date][year]" class="form-control"><option value="2016">2016</option><option value="2017">2017</option><option value="2018">2018</option><option value="2019">2019</option><option value="2020">2020</option><option value="2021">2021</option><option value="2022">2022</option><option value="2023">2023</option><option value="2024">2024</option><option value="2025">2025</option><option value="2026">2026</option></select><div class="sr-only"><label class="required" for="assistance_createdAt_time_hour">Hour</label></div><select id="assistance_createdAt_time_hour" name="assistance[createdAt][time][hour]" class="form-control"><option value="0">00</option><option value="1">01</option><option value="2">02</option><option value="3">03</option><option value="4">04</option><option value="5">05</option><option value="6">06</option><option value="7">07</option><option value="8">08</option><option value="9">09</option><option value="10">10</option><option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14">14</option><option value="15">15</option><option value="16">16</option><option value="17">17</option><option value="18">18</option><option value="19">19</option><option value="20">20</option><option value="21">21</option><option value="22">22</option><option value="23">23</option></select>:<div class="sr-only"><label class="required" for="assistance_createdAt_time_minute">Minute</label></div><select id="assistance_createdAt_time_minute" name="assistance[createdAt][time][minute]" class="form-control"><option value="0">00</option><option value="1">01</option><option value="2">02</option><option value="3">03</option><option value="4">04</option><option value="5">05</option><option value="6">06</option><option value="7">07</option><option value="8">08</option><option value="9">09</option><option value="10">10</option><option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14">14</option><option value="15">15</option><option value="16">16</option><option value="17">17</option><option value="18">18</option><option value="19">19</option><option value="20">20</option><option value="21">21</option><option value="22">22</option><option value="23">23</option><option value="24">24</option><option value="25">25</option><option value="26">26</option><option value="27">27</option><option value="28">28</option><option value="29">29</option><option value="30">30</option><option value="31">31</option><option value="32">32</option><option value="33">33</option><option value="34">34</option><option value="35">35</option><option value="36">36</option><option value="37">37</option><option value="38">38</option><option value="39">39</option><option value="40">40</option><option value="41">41</option><option value="42">42</option><option value="43">43</option><option value="44">44</option><option value="45">45</option><option value="46">46</option><option value="47">47</option><option value="48">48</option><option value="49">49</option><option value="50">50</option><option value="51">51</option><option value="52">52</option><option value="53">53</option><option value="54">54</option><option value="55">55</option><option value="56">56</option><option value="57">57</option><option value="58">58</option><option value="59">59</option></select></div></fieldset>
            
            <div class="form-group">
                <button class="btn btn-primary">Save</button>
            </div>
            <input type="hidden" id="assistance__token" name="assistance[_token]" value="u_CH6pE_JEs966n0rVIw6vgvlEptfkZOxyTeEa8UiOA"></form>
            
            
        </div>
        <div class="col-md-4 mt-4">
            <div class="bg-save">
                <h3>Filter Contibutors</h3>
                <hr>
                <form method="post" class=" p-2">
                <div><div class="form-group"><label style="font-weight: bold;" for="minCashBalance">Mincashbalance</label><input type="number" id="minCashBalance" name="minCashBalance" class="form-control"></div><div class="form-group"><label style="font-weight: bold;" for="maxCashBalance">Maxcashbalance</label><input type="number" id="maxCashBalance" name="maxCashBalance" class="form-control"></div><input type="hidden" id="_token" name="_token" value="taFykygyzqxXQfoEzYJs9W1A6BFjV_rTC1TRLGS30v8"></div>
                <button type="submit" class="btn btn-primary" id="filter_form_btn">filter</button>
                </form>
            </div>
            
        </div>
    </div>
        );
    }
}

export default contributor;