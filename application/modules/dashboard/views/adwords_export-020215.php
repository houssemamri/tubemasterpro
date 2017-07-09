<style type="text/css">
  #video_ads_list {

  }

  #video_ads_list li {
/*       background: #ccc; */
      padding: 10px;
  }

  #video_ads_list li:hover {
    cursor: pointer;
  }

  #video_ads_list li .remove-button {
      margin-left: 10px;
  }

 li.vid-ads-item-selected {
/*       background: #3B99FC !important; */
  }

  #video_ads_list li span {
    text-indent: 20px;
  }

  .glyphicon-ok:before {
    margin-right: 10px;
    color: rgb(92, 184, 92);
    text-indent: 20px;
  }
  
  #mobile_bid_mdifier::after {
  	content: "%";
  	position: absolute;
  }

  #error-msg {
    text-align: center;
    margin-top: -42px;
    height: 40px;
    width: 100%;
    position: absolute;
    top: 0;
    left: 0;
  }

  #error-msg span {
    font-size: 1.6em;
    color: rgb(217, 83, 79);
  }

  #language option[selected] {
    background-color: #3B99FC;
  }
  
  /*
#btnExportCSV {
  	padding: 12px !important 24px !important;	
  }
*/


</style>
<p id="error-msg" class="bg-danger"><span></span> <input id="error-focus" type="text"  readonly value="" style="position:absolute;"/></p>

<h1>
    Campaign
</h1>
<form id="adwords_form" class="form-horizontal" rel="async" action="" autocomplete="off">
    <div class="col-sm-6">
      <div class="form-group">
          <label class="col-sm-3 control-label" for="Campaign">
              *Campaign Name 
          </label>
          <div class="col-sm-9">
              <input class="form-control" id="file_name" name="file_name" type="text" placeholder="Campaign Name">
          </div>
      </div>
      <div class="form-group">    
          <label class="col-sm-3 control-label" for="Campaign">
              *Video Ads 
          </label>
           <div class="col-sm-9">
            <div class="input-group">
              <input class="form-control" type="text" id="add_video_ads" name="add_video_ads" placeholder="Youtube Ad URL">
              <span class="input-group-btn">
                  <button class="btn btn-success" type="button" id="add_video_ad" data-action="<?php echo site_url('dashboard/campaign_ajax'); ?>">
                      Add URL
                  </button>
              </span>
            </div>
              <select class="form-control" id="video_ads" name="video_ads" multiple="" style="min-height:210px;margin-top:10px;width:430px">
              
              </select>
              <ul id="video_ads_list"></ul>
          </div>
      </div>
      <div class="form-group">
          <label class="col-sm-3 control-label" for="Campaign">
              *Display URL
          </label>
          <div class="col-sm-9">
              <input class="form-control" id="display_url" name="display_url" type="text" placeholder="Enter URL">
          </div>
      </div>
      <div class="form-group">
          <label class="col-sm-3 control-label" for="Campaign">
              *Destination URL
          </label>
          <div class="col-sm-9">
              <input class="form-control" id="destination_url" name="destination_url" type="text" value="https://www.nathanhague.com/get-100-clients-in-30-days/">
          </div>
      </div>
      <div class="form-group">
          <label class="col-sm-3 control-label" for="Campaign">
              *Target List </br> <span id="total_target_vid" style="display:none;">(<font style="color:#449d44">0</font>)</span>
          </label>
           <div class="col-sm-9 checkbox" style="overflow-y:auto">
              <ul id="target_list" name="target_list" style="min-height:210px;list-style: none;
padding-left: 0px;">
                  <?php if($targets){
                        echo $targets;
                  }?>              
              </ul>
          </div>
      </div>
      <div class="form-group">
        <label class="col-sm-3 control-label" for="Campaign">
              Daily Budget
        </label>
        <div class="col-sm-3">
              <input class="form-control" id="budget" name="budget" type="text" placeholder="" value="5">
        </div>
        <div>
            *Currency based on Google Adwords Account
        </div>
      </div>
      <div class="form-group">
        <label class="col-sm-3 control-label" for="Campaign">
              Max. CPV $
        </label>
        <div class="col-sm-3">
              <input class="form-control" id="max_cpv" name="max_cpv" type="text" placeholder="input.form-control" value="0.10">
        </div>
      </div>
    </div>
    <div class="col-sm-6">
    </div>
    <div class="col-sm-12">
      <div id="options" style="margin-bottom:20px">
        <button type="button" id="toggle_option" class="btn btn-link">Show Options</button>
      </div>
      <div class="added_options" style="display:none">
        <div class="col-sm-6">
          <div class="form-group">
            <label class="col-sm-4 control-label" for="Campaign">
                Start Date
            </label>
            <div class="col-sm-5">
                <input class="form-control" readonly id="start_date" name="start_date" type="text" placeholder="" value="<?php date_default_timezone_set('America/Los_Angeles');echo date("d M Y");?>">
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-4 control-label" for="Campaign">
              Language </br> <span>(<font  id="language_count">1</font>)</span>
            </label>
            <div class="col-sm-5">
              <select class="form-control" multiple="" id="language" name="language" style="min-height:210px">
              	  <option value="English" selected>English</option>
                  <option value="Arabic">Arabic</option>  
                  <option value="Bulgarian">Bulgarian</option> 
                  <option value="Catalan">Catalan</option> 
                  <option value="Chinese (Simplified)">Chinese (Simplified)</option> 
                  <option value="Chinese (Traditional)">Chinese (Traditional)</option>  
                  <option value="Croatian">Croatian</option>  
                  <option value="Czech">Czech</option> 
                  <option value="Danish">Danish</option>  
                  <option value="Dutch">Dutch</option>  
                  <option value="Estonian">Estonian</option>  
                  <option value="Filipino">Filipino</option> 
                  <option value="Finnish">Finnish</option> 
                  <option value="French">French</option>  
                  <option value="German">German</option>  
                  <option value="Greek">Greek</option>  
                  <option value="Hebrew">Hebrew</option>  
                  <option value="Hindi">Hindi</option> 
                  <option value="Hungarian">Hungarian</option> 
                  <option value="Icelandic">Icelandic</option> 
                  <option value="Indonesian">Indonesian</option>  
                  <option value="Italian">Italian</option> 
                  <option value="Japanese">Japanese</option>  
                  <option value="Korean">Korean</option>  
                  <option value="Latvian">Latvian</option> 
                  <option value="Lithuanian">Lithuanian</option>  
                  <option value="Malay">Malay</option>
                  <option value="Norwegian">Norwegian</option> 
                  <option value="Persian">Persian</option> 
                  <option value="Polish">Polish</option>
                  <option value="Portuguese">Portuguese</option>  
                  <option value="Romanian">Romanian</option>  
                  <option value="Russian">Russian</option> 
                  <option value="Serbian">Serbian</option> 
                  <option value="Slovak">Slovak</option>  
                  <option value="Slovenian">Slovenian</option> 
                  <option value="Spanish">Spanish</option>  
                  <option value="Swedish">Swedish</option> 
                  <option value="Thai">Thai</option>  
                  <option value="Turkish">Turkish</option> 
                  <option value="Ukrainian">Ukrainian</option> 
                  <option value="Urdu">Urdu</option>  
                  <option value="Vietnamese">Vietnamese</option> 
              </select>
            </div>
          </div>
        <div class="form-group">
        <label class="col-sm-4 control-label">Delivery Method</label>
        <div class="col-sm-5">
            <select class="form-control" id="delivery_method" name="delivery_method">
                <option value="standard" selected="">Standard</option>
                <option value="accelerated">Accelerated</option>
            </select>
        </div>
      </div>
      <div class="form-group">
          <label class="col-sm-4 control-label" for="Campaign">
              Countries </br> <span>(<font  id="country_count">1</font>)</span>
          </label>
           <div class="col-sm-5">
              <select class="form-control" multiple="" id="countries" name="countries" style="min-height:210px;width:235px">
                  <option value="Australia">Australia</option>
                  <option value="United Kingdom">United Kingdom</option>
                  <option value="United States of America" selected>United States of America</option>
                  <option value="Afganistan">Afghanistan</option>
                  <option value="Albania">Albania</option>
                  <option value="Algeria">Algeria</option>
                  <option value="American Samoa">American Samoa</option>
                  <option value="Andorra">Andorra</option>
                  <option value="Angola">Angola</option>
                  <option value="Anguilla">Anguilla</option>
                  <option value="Antigua &amp; Barbuda">Antigua &amp; Barbuda</option>
                  <option value="Argentina">Argentina</option>
                  <option value="Armenia">Armenia</option>
                  <option value="Aruba">Aruba</option>
                  <option value="Austria">Austria</option>
                  <option value="Azerbaijan">Azerbaijan</option>
                  <option value="Bahamas">Bahamas</option>
                  <option value="Bahrain">Bahrain</option>
                  <option value="Bangladesh">Bangladesh</option>
                  <option value="Barbados">Barbados</option>
                  <option value="Belarus">Belarus</option>
                  <option value="Belgium">Belgium</option>
                  <option value="Belize">Belize</option>
                  <option value="Benin">Benin</option>
                  <option value="Bermuda">Bermuda</option>
                  <option value="Bhutan">Bhutan</option>
                  <option value="Bolivia">Bolivia</option>
                  <option value="Bonaire">Bonaire</option>
                  <option value="Bosnia &amp; Herzegovina">Bosnia &amp; Herzegovina</option>
                  <option value="Botswana">Botswana</option>
                  <option value="Brazil">Brazil</option>
                  <option value="British Indian Ocean Ter">British Indian Ocean Ter</option>
                  <option value="Brunei">Brunei</option>
                  <option value="Bulgaria">Bulgaria</option>
                  <option value="Burkina Faso">Burkina Faso</option>
                  <option value="Burundi">Burundi</option>
                  <option value="Cambodia">Cambodia</option>
                  <option value="Cameroon">Cameroon</option>
                  <option value="Canada">Canada</option>
                  <option value="Canary Islands">Canary Islands</option>
                  <option value="Cape Verde">Cape Verde</option>
                  <option value="Cayman Islands">Cayman Islands</option>
                  <option value="Central African Republic">Central African Republic</option>
                  <option value="Chad">Chad</option>
                  <option value="Channel Islands">Channel Islands</option>
                  <option value="Chile">Chile</option>
                  <option value="China">China</option>
                  <option value="Christmas Island">Christmas Island</option>
                  <option value="Cocos Island">Cocos Island</option>
                  <option value="Colombia">Colombia</option>
                  <option value="Comoros">Comoros</option>
                  <option value="Congo">Congo</option>
                  <option value="Cook Islands">Cook Islands</option>
                  <option value="Costa Rica">Costa Rica</option>
                  <option value="Cote DIvoire">Cote D'Ivoire</option>
                  <option value="Croatia">Croatia</option>
                  <option value="Cuba">Cuba</option>
                  <option value="Curaco">Curacao</option>
                  <option value="Cyprus">Cyprus</option>
                  <option value="Czech Republic">Czech Republic</option>
                  <option value="Denmark">Denmark</option>
                  <option value="Djibouti">Djibouti</option>
                  <option value="Dominica">Dominica</option>
                  <option value="Dominican Republic">Dominican Republic</option>
                  <option value="East Timor">East Timor</option>
                  <option value="Ecuador">Ecuador</option>
                  <option value="Egypt">Egypt</option>
                  <option value="El Salvador">El Salvador</option>
                  <option value="Equatorial Guinea">Equatorial Guinea</option>
                  <option value="Eritrea">Eritrea</option>
                  <option value="Estonia">Estonia</option>
                  <option value="Ethiopia">Ethiopia</option>
                  <option value="Falkland Islands">Falkland Islands</option>
                  <option value="Faroe Islands">Faroe Islands</option>
                  <option value="Fiji">Fiji</option>
                  <option value="Finland">Finland</option>
                  <option value="France">France</option>
                  <option value="French Guiana">French Guiana</option>
                  <option value="French Polynesia">French Polynesia</option>
                  <option value="French Southern Ter">French Southern Ter</option>
                  <option value="Gabon">Gabon</option>
                  <option value="Gambia">Gambia</option>
                  <option value="Georgia">Georgia</option>
                  <option value="Germany">Germany</option>
                  <option value="Ghana">Ghana</option>
                  <option value="Gibraltar">Gibraltar</option>
                  <option value="Great Britain">Great Britain</option>
                  <option value="Greece">Greece</option>
                  <option value="Greenland">Greenland</option>
                  <option value="Grenada">Grenada</option>
                  <option value="Guadeloupe">Guadeloupe</option>
                  <option value="Guam">Guam</option>
                  <option value="Guatemala">Guatemala</option>
                  <option value="Guinea">Guinea</option>
                  <option value="Guyana">Guyana</option>
                  <option value="Haiti">Haiti</option>
                  <option value="Hawaii">Hawaii</option>
                  <option value="Honduras">Honduras</option>
                  <option value="Hong Kong">Hong Kong</option>
                  <option value="Hungary">Hungary</option>
                  <option value="Iceland">Iceland</option>
                  <option value="India">India</option>
                  <option value="Indonesia">Indonesia</option>
                  <option value="Iran">Iran</option>
                  <option value="Iraq">Iraq</option>
                  <option value="Ireland">Ireland</option>
                  <option value="Isle of Man">Isle of Man</option>
                  <option value="Israel">Israel</option>
                  <option value="Italy">Italy</option>
                  <option value="Jamaica">Jamaica</option>
                  <option value="Japan">Japan</option>
                  <option value="Jordan">Jordan</option>
                  <option value="Kazakhstan">Kazakhstan</option>
                  <option value="Kenya">Kenya</option>
                  <option value="Kiribati">Kiribati</option>
                  <option value="Korea North">Korea North</option>
                  <option value="Korea Sout">Korea South</option>
                  <option value="Kuwait">Kuwait</option>
                  <option value="Kyrgyzstan">Kyrgyzstan</option>
                  <option value="Laos">Laos</option>
                  <option value="Latvia">Latvia</option>
                  <option value="Lebanon">Lebanon</option>
                  <option value="Lesotho">Lesotho</option>
                  <option value="Liberia">Liberia</option>
                  <option value="Libya">Libya</option>
                  <option value="Liechtenstein">Liechtenstein</option>
                  <option value="Lithuania">Lithuania</option>
                  <option value="Luxembourg">Luxembourg</option>
                  <option value="Macau">Macau</option>
                  <option value="Macedonia">Macedonia</option>
                  <option value="Madagascar">Madagascar</option>
                  <option value="Malaysia">Malaysia</option>
                  <option value="Malawi">Malawi</option>
                  <option value="Maldives">Maldives</option>
                  <option value="Mali">Mali</option>
                  <option value="Malta">Malta</option>
                  <option value="Marshall Islands">Marshall Islands</option>
                  <option value="Martinique">Martinique</option>
                  <option value="Mauritania">Mauritania</option>
                  <option value="Mauritius">Mauritius</option>
                  <option value="Mayotte">Mayotte</option>
                  <option value="Mexico">Mexico</option>
                  <option value="Midway Islands">Midway Islands</option>
                  <option value="Moldova">Moldova</option>
                  <option value="Monaco">Monaco</option>
                  <option value="Mongolia">Mongolia</option>
                  <option value="Montserrat">Montserrat</option>
                  <option value="Morocco">Morocco</option>
                  <option value="Mozambique">Mozambique</option>
                  <option value="Myanmar">Myanmar</option>
                  <option value="Nambia">Nambia</option>
                  <option value="Nauru">Nauru</option>
                  <option value="Nepal">Nepal</option>
                  <option value="Netherland Antilles">Netherland Antilles</option>
                  <option value="Netherlands">Netherlands (Holland, Europe)</option>
                  <option value="Nevis">Nevis</option>
                  <option value="New Caledonia">New Caledonia</option>
                  <option value="New Zealand">New Zealand</option>
                  <option value="Nicaragua">Nicaragua</option>
                  <option value="Niger">Niger</option>
                  <option value="Nigeria">Nigeria</option>
                  <option value="Niue">Niue</option>
                  <option value="Norfolk Island">Norfolk Island</option>
                  <option value="Norway">Norway</option>
                  <option value="Oman">Oman</option>
                  <option value="Pakistan">Pakistan</option>
                  <option value="Palau Island">Palau Island</option>
                  <option value="Palestine">Palestine</option>
                  <option value="Panama">Panama</option>
                  <option value="Papua New Guinea">Papua New Guinea</option>
                  <option value="Paraguay">Paraguay</option>
                  <option value="Peru">Peru</option>
                  <option value="Phillipines">Philippines</option>
                  <option value="Pitcairn Island">Pitcairn Island</option>
                  <option value="Poland">Poland</option>
                  <option value="Portugal">Portugal</option>
                  <option value="Puerto Rico">Puerto Rico</option>
                  <option value="Qatar">Qatar</option>
                  <option value="Republic of Montenegro">Republic of Montenegro</option>
                  <option value="Republic of Serbia">Republic of Serbia</option>
                  <option value="Reunion">Reunion</option>
                  <option value="Romania">Romania</option>
                  <option value="Russia">Russia</option>
                  <option value="Rwanda">Rwanda</option>
                  <option value="St Barthelemy">St Barthelemy</option>
                  <option value="St Eustatius">St Eustatius</option>
                  <option value="St Helena">St Helena</option>
                  <option value="St Kitts-Nevis">St Kitts-Nevis</option>
                  <option value="St Lucia">St Lucia</option>
                  <option value="St Maarten">St Maarten</option>
                  <option value="St Pierre &amp; Miquelon">St Pierre &amp; Miquelon</option>
                  <option value="St Vincent &amp; Grenadines">St Vincent &amp; Grenadines</option>
                  <option value="Saipan">Saipan</option>
                  <option value="Samoa">Samoa</option>
                  <option value="Samoa American">Samoa American</option>
                  <option value="San Marino">San Marino</option>
                  <option value="Sao Tome &amp; Principe">Sao Tome &amp; Principe</option>
                  <option value="Saudi Arabia">Saudi Arabia</option>
                  <option value="Senegal">Senegal</option>
                  <option value="Serbia">Serbia</option>
                  <option value="Seychelles">Seychelles</option>
                  <option value="Sierra Leone">Sierra Leone</option>
                  <option value="Singapore">Singapore</option>
                  <option value="Slovakia">Slovakia</option>
                  <option value="Slovenia">Slovenia</option>
                  <option value="Solomon Islands">Solomon Islands</option>
                  <option value="Somalia">Somalia</option>
                  <option value="South Africa">South Africa</option>
                  <option value="Spain">Spain</option>
                  <option value="Sri Lanka">Sri Lanka</option>
                  <option value="Sudan">Sudan</option>
                  <option value="Suriname">Suriname</option>
                  <option value="Swaziland">Swaziland</option>
                  <option value="Sweden">Sweden</option>
                  <option value="Switzerland">Switzerland</option>
                  <option value="Syria">Syria</option>
                  <option value="Tahiti">Tahiti</option>
                  <option value="Taiwan">Taiwan</option>
                  <option value="Tajikistan">Tajikistan</option>
                  <option value="Tanzania">Tanzania</option>
                  <option value="Thailand">Thailand</option>
                  <option value="Togo">Togo</option>
                  <option value="Tokelau">Tokelau</option>
                  <option value="Tonga">Tonga</option>
                  <option value="Trinidad &amp; Tobago">Trinidad &amp; Tobago</option>
                  <option value="Tunisia">Tunisia</option>
                  <option value="Turkey">Turkey</option>
                  <option value="Turkmenistan">Turkmenistan</option>
                  <option value="Turks &amp; Caicos Is">Turks &amp; Caicos Is</option>
                  <option value="Tuvalu">Tuvalu</option>
                  <option value="Uganda">Uganda</option>
                  <option value="Ukraine">Ukraine</option>
                  <option value="United Arab Erimates">United Arab Emirates</option>
                  <option value="Uraguay">Uruguay</option>
                  <option value="Uzbekistan">Uzbekistan</option>
                  <option value="Vanuatu">Vanuatu</option>
                  <option value="Vatican City State">Vatican City State</option>
                  <option value="Venezuela">Venezuela</option>
                  <option value="Vietnam">Vietnam</option>
                  <option value="Virgin Islands (Brit)">Virgin Islands (Brit)</option>
                  <option value="Virgin Islands (USA)">Virgin Islands (USA)</option>
                  <option value="Wake Island">Wake Island</option>
                  <option value="Wallis &amp; Futana Is">Wallis &amp; Futana Is</option>
                  <option value="Yemen">Yemen</option>
                  <option value="Zaire">Zaire</option>
                  <option value="Zambia">Zambia</option>
                  <option value="Zimbabwe">Zimbabwe</option>
              </select>
          </div>
        </div>
      </div>

      <div class="col-sm-6">
      	<div class="form-group">
            <label class="col-sm-5 control-label" for="Campaign">
                End Date
            </label>
            <div class="col-sm-5">
                <input class="form-control" readonly id="end_date" name="end_date" type="text" placeholder="" value="">
            </div>
         </div>
        <div class="form-group">
            <label class="col-sm-5 control-label" for="Campaign">
                Mobile Bid Modifier
            </label>
            <div class="col-sm-6">
                <select id="mbm-type" style="width:135px;inline:block">
                  <option value="0" selected>Choose</option>
                  <option value="">Increase By</option>
                  <option value="-">Decrease By</option>
                </select>
                <input style="width:30px;inline:block" id="mobile_bid_mdifier" name="mobile_bid_mdifier" type="text" placeholder="" maxlength="3" value="0">
              	<span style="width:10px;inline:block">%</span>
            </div>
        </div>
        <div class="form-group">
          <label class="col-sm-5 control-label">Age</label>
          <div class="col-sm-6">
              <div class="checkbox">
                  <label>
                      <input type="checkbox" name="age" class="age_checkbox" value="Unknown" checked>
                      Unknown
                  </label>
              </div>
              <div class="checkbox">
                  <label>
                      <input type="checkbox" name="age" class="age_checkbox" value="18 - 24" checked>
                      18 - 24
                  </label>
              </div>
              <div class="checkbox">
                  <label>
                      <input type="checkbox" name="age" class="age_checkbox" value="25 - 34" checked>
                      25 - 34
                  </label>
              </div>
              <div class="checkbox">
                  <label>
                      <input type="checkbox" name="age" class="age_checkbox" value="35 - 44" checked>
                      35 - 44
                  </label>
              </div>
              <div class="checkbox">
                  <label>
                      <input type="checkbox" name="age" class="age_checkbox" value="45 - 54" checked>
                      45 - 54
                  </label>
              </div>
              <div class="checkbox">
                  <label>
                      <input type="checkbox" name="age" class="age_checkbox" value="55 - 64" checked>
                      55 - 64
                  </label>
              </div>
              <div class="checkbox">
                  <label>
                      <input type="checkbox" name="age" class="age_checkbox" value="65+" checked>
                      65+
                  </label>
              </div>
          </div>
        </div>
    
        <div class="form-group">
            <label class="col-sm-3 control-label">Gender</label>
            <!-- <div class="col-sm-offset-3 col-sm-9"> -->
                <label class="checkbox-inline">
                    <input type="checkbox" value="Male" name="gender" class="gender_checkbox" checked>
                    Male
                </label>
                <label class="checkbox-inline">
                    <input type="checkbox" value="Female" name="gender" class="gender_checkbox" checked>
                    Female
                </label>
                <label class="checkbox-inline">
                    <input type="checkbox" value="Unknown" name="gender" class="gender_checkbox" checked>
                    Unknown
                </label>
            <!-- </div> -->
        </div>
    </div>
  </div>
  <div id="csv_table" style="display:none">
    <table border="0" cellpadding="0" cellspacing="0" width="9713" style="border-collapse:
 collapse;table-layout:fixed">
 <colgroup><col width="192">
 <col width="119">
 <col width="160">
 <col width="142">
 <col width="126">
 <col width="188">
 <col width="203">
 <col width="159">
 <col width="139">
 <col width="140">
 <col width="143">
 <col width="126">
 <col width="184">
 <col width="181">
 <col width="184">
 <col width="168">
 <col width="201">
 <col width="171">
 <col width="174">
 <col width="163">
 <col width="171">
 <col width="162">
 <col width="163">
 <col width="171">
 <col width="166">
 <col width="184">
 <col width="202">
 <col width="175">
 <col width="186">
 <col width="203">
 <col width="210">
 <col width="169" span="2">
 <col width="209">
 <col width="212">
 <col width="184">
 <col width="204">
 <col width="176">
 <col width="186">
 <col width="175">
 <col width="196">
 <col width="186">
 <col width="191">
 <col width="178">
 <col width="180">
 <col width="130">
 <col width="123">
 <col width="152">
 <col width="185">
 <col width="184">
 <col width="166">
 <col width="165">
 <col width="186">
 <col width="220">
 <col width="184">
 <col width="147">
 </colgroup><tbody><tr height="13">
  <td height="13" width="192">Input campaigns here</td>
  <td width="119" colspan="2">"Campaign, locale=en_US"</td>
  <td width="160"></td>
  <td width="142"></td>
  <td width="126"></td>
  <td width="188"></td>
  <td width="203"></td>
  <td width="159"></td>
  <td width="139"></td>
  <td width="140"></td>
  <td width="143"></td>
  <td width="126"></td>
  <td width="184"></td>
  <td width="181"></td>
  <td width="184"></td>
  <td width="168"></td>
  <td width="201"></td>
  <td width="171"></td>
  <td width="174"></td>
  <td width="163"></td>
  <td width="171"></td>
  <td width="162"></td>
  <td width="163"></td>
  <td width="171"></td>
  <td width="166"></td>
  <td width="184"></td>
  <td width="202"></td>
  <td width="175"></td>
  <td width="186"></td>
  <td width="203"></td>
  <td width="210"></td>
  <td width="169"></td>
  <td width="169"></td>
  <td width="209"></td>
  <td width="212"></td>
  <td width="184"></td>
  <td width="204"></td>
  <td width="176"></td>
  <td width="186"></td>
  <td width="175"></td>
  <td width="196"></td>
  <td width="186"></td>
  <td width="191"></td>
  <td width="178"></td>
  <td width="180"></td>
  <td width="130"></td>
  <td width="123"></td>
  <td width="152"></td>
  <td width="185"></td>
  <td width="184"></td>
  <td width="166"></td>
  <td width="165"></td>
  <td width="186"></td>
  <td width="220"></td>
  <td width="184"></td>
  <td width="147"></td>
 </tr>
 <tr>
  <td>Action</td>
  <td>Status</td>
  <td>Campaign</td>
  <td>Budget</td>
  <td>Budget ID</td>
  <td>Network</td>
  <td>Delivery method</td>
  <td>Start date</td>
  <td>End date</td>
  <td>Ad rotation</td>
  <td>Frequency cap</td>
  <td>Mobile bid modifier</td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
 </tr>
 <tr>
  <td>Add</td>
  <td>Enabled</td>
  <td id="campaign_name_value"></td>
  <td id="campaign_budget"></td>
  <td>#N/A</td>
  <td>YouTube Videos</td>
  <td id="delivery_mode_value"></td>
  <td id="start_date_value"></td>
  <td id="end_date_value">#N/A</td>
  <td>Optimize for views</td>
  <td align="center">#N/A</td>
  <td class="xl24" align="right" id="mobile_bid_modifier_value"></td>
  <td align="right"></td>
  <td align="right"></td>
  <td align="right"></td>
  <td align="right"></td>
  <td></td>
  <td class="xl25" align="right"></td>
  <td align="right"></td>
  <td align="right"></td>
  <td align="right"></td>
  <td class="xl25" align="right"></td>
  <td align="right"></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
 </tr>
 <tr>
  <td colspan="56"></td>
 </tr>
 <tr>
  <td>Input ads here</td>
  <td colspan="2">"Campaign, locale=en_US"</td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
 </tr>
 <tr id="targeting_ads">
  <td>Action</td>
  <td>Status</td>
  <td>Ad</td>
  <td id="video_id_value">Video id</td>
  <td>Thumbnail</td>
  <td>Headline</td>
  <td>Description line one</td>
  <td>Description line two</td>
  <td>Display Url</td>
  <td>Destination Url</td>
  <td>YouTube destination</td>
  <td>Showing on</td>
  <td>Companion banner</td>
  <td>Enable ad for</td>
  <td>Campaign</td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
 </tr>
 <tr>
  <td colspan="56"></td>
 </tr>
 <tr>
  <td>Input targeting groups here</td>
  <td colspan="2">"Campaign, locale=en_US"</td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
 </tr>
 <tr id="targeting_targettinggroups">
  <td>Action</td>
  <td>Status</td>
  <td>Targeting group</td>
  <td>Campaign</td>
  <td>Max CPV</td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
 </tr>
 <tr>
  <td colspan="56"></td>
 </tr>
 <tr>
  <td>Input targets here</td>
  <td colspan="2">"Campaign, locale=en_US"</td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
 </tr>
 <tr id="targeting_targets">
  <td>Action</td>
  <td>Type</td>
  <td>Status</td>
  <td>Target</td>
  <td>Targeting group</td>
  <td>Max CPV</td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
 </tr>
 <tr>
  <td colspan="56"></td>
 </tr>
 <tr>
  <td>Input targets here (campaign settings)</td>
  <td colspan="2">"Campaign, locale=en_US"</td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
 </tr>
 <tr id="targeting_settings">
  <td>Action</td>
  <td>Type</td>
  <td>Campaign target</td>
  <td>Campaign</td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
 </tr>
</tbody></table>
  </div>
      
    
    <div class="col-sm-12">
      <div class="form-group">
        <!-- <button class="btn btn-primary" type="button" id="btnExportCSV" data-action="<?php echo site_url('dashboard/dashboard_ajax'); ?>">Export</button> -->
        <a class="btn btn-primary btn-lg" type="button" id="btnExportCSV" data-action="<?php echo site_url('dashboard/campaign_ajax'); ?>">Export</a>
      </div>
    </div> 
</form>
<div id="search_result"></div>


<div class="modal fade" id="video_search_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">

    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="myModalLabel">Message</h4>
      </div>
      <div class="modal-body" style="text-align:center;">
          <p>
            Please type a video to search
          </p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
