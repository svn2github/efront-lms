<h1>TextMe module help</h1>

<a name="index">&nbsp;</a>
<h2>Index</h2>
<ol type="I">
    <li><a href="{$T_TEXTME_BASEURL}&cat=help#whatis">What is TextMe?</a></li>
    <li><a href="{$T_TEXTME_BASEURL}&cat=help#lessons">Lessons accounts</a>
        <ol type="A">
            <li><a href="{$T_TEXTME_BASEURL}&cat=help#lessons_edit">Edit lesson account</a></li>
        </ol>
    </li>
    <li><a href="{$T_TEXTME_BASEURL}&cat=help#gateways">Gateways</a>
        <ol type="A">
            <li><a href="{$T_TEXTME_BASEURL}&cat=help#gateways_supported">Supported gateways</a></li>
            <li><a href="{$T_TEXTME_BASEURL}&cat=help#gateways_add">Add gateway</a></li>
            <li><a href="{$T_TEXTME_BASEURL}&cat=help#gateways_edit">Edit gateway</a></li>
            <li><a href="{$T_TEXTME_BASEURL}&cat=help#gateways_delete">Delete gateway</a></li>
            <li><a href="{$T_TEXTME_BASEURL}&cat=help#gateways_test">Test gateway</a></li>
            <li><a href="{$T_TEXTME_BASEURL}&cat=help#gateways_activate">Activate/Deactivate gateway</a></li>
        </ol>
    </li>
</ol>

<a name="whatis">&nbsp;</a>
<h2>
    What is TextMe?
    <a href="{$T_TEXTME_BASEURL}&cat=help#index">
        <img src="{$T_TEXTME_BASELINK}assets/images/16/arrow_up_blue.png" alt="Back to index" title="Back to index" />
    </a>
</h2>

<p>TextMe is a module for sending sms alerts.</p>
<p>TextMe gives the ability to professors to send sms alerts to lessons's participants.<br/></p>


<a name="lessons">&nbsp;</a>
<h2>
    Lessons accounts
    <a href="{$T_TEXTME_BASEURL}&cat=help#index"><img src="{$T_TEXTME_BASELINK}assets/images/16/arrow_up_blue.png" alt="Back to index" title="Back to index" /></a>
</h2>

<p>
    Through TextMe's interface you can view which of the lessons have activated TextMe module.<br/>
    TextMe maintains an account for each lesson that has activated the module. You can review their<br/>
    account details and edit the amount of credits that will be assigned to each lesson.<br/>

    That's right! TextMe uses credits for charging every sms alert sent by TextMe. You can set a given amount<br/>
    of credits for each lesson. Adjust it later as the course goes on or even set it to unlimited!<br/>
</p>


<a name="lessons_edit">&nbsp;</a>
<h3>
    Edit lesson account
    <a href="{$T_TEXTME_BASEURL}&cat=help#index"><img src="{$T_TEXTME_BASELINK}assets/images/16/arrow_up_blue.png" alt="Back to index" title="Back to index" /></a>
</h3>

<ol>
    <li>Click on "TextMe" icon from the "Control Center" panel</li>
    <li>Click on "Lessons accounts" tab.</li>
    <li>Click on "Edit lesson account" icon.</li>
    <li>Edit "Credits" field. (Leave blank for unlimited remaining credits)</li>
    <li>Click submit</li>
</ol>


<a name="gateways">&nbsp;</a>
<h2>
    Gateways
    <a href="{$T_TEXTME_BASEURL}&cat=help#index">
        <img src="{$T_TEXTME_BASELINK}assets/images/16/arrow_up_blue.png" alt="Back to index" title="Back to index" />
    </a>
</h2>

<p>
    Sms gateways are online providers of sms services.<br/>
    TextMe's functionality depends on the services of sms gateways.<br/><br/>

    You need to have (or create) an account in one of the supported gateways<br/>
    in order to activate sms alerts through TextMe.<br/><br/>

    TextMe module will still function without defining a default gateway but all<br/>
    alerts will only be available locally for its users.<br/><br/>

    Through TextMe's interface you can add one or more gateway accounts for routing<br/>
    sms alerts. You can even add multiple accounts for the same gateway. However only<br/>
    one gateway can be active and used at any time.<br/><br/>

    Below you can see a list of the gateways that TextMe currently supports.
</p>

<a name="gateways_supported">&nbsp;</a>
<h3>
    Supported sms gateways
    <a href="{$T_TEXTME_BASEURL}&cat=help#index">
        <img src="{$T_TEXTME_BASELINK}assets/images/16/arrow_up_blue.png" alt="Back to index" title="Back to index" />
    </a>
</h3>

<p>
    TextMe supports the following gateways:
</p>

<ul>
    <li><a href="http://www.ez4usms.com" target="_blank">www.ez4usms.com</a></li>
    <li><a href="http://www.smsn.gr" target="_blank">www.smsn.gr</a></li>
    <li><a href="http://www.smsone.gr" target="_blank">www.smsone.gr</a></li>
    <li><a href="http://www.smsthemall.com'" target="_blank">www.smsthemall.com</a></li>
</ul>

<a name="gateways_add">&nbsp;</a>
<h3>
    Add gateway
    <a href="{$T_TEXTME_BASEURL}&cat=help#index">
        <img src="{$T_TEXTME_BASELINK}assets/images/16/arrow_up_blue.png" alt="Back to index" title="Back to index" />
    </a>
</h3>

<ol>
    <li>Click on "TextMe" icon from the "Control Center" panel</li>
    <li>Click on "Sms Gateways" tab.</li>
    <li>Click on "Add sms gateway" link.</li>
    <li>Select a gateway.</li>
    <li>Enter a name for this gateway. It's up to you how you will name this gateway! </li>
    <li>Enter gateway's parameters. Each gateway requires different parameters in order to function correctly.<br/>
        In the table bellow you can see the mandatory parameters for each gateway.
        <br/>
        <br/>
        <table border="1" cellpadding="4">
            <thead>
                <tr style="background-color: lightgray;">
                    <td>Gateway</td>
                    <td>Required parameters</td>
                    <td>Additional features</td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>www.ez4usms.com</td>
                    <td>username, password</td>
                    <td>Supports delivery reports, Does not support schedule sms</td>
                </tr>
                <tr>
                    <td>www.smsone.com</td>
                    <td>username, password</td>
                    <td>Supports delivery reports, Does not support schedule sms</td>
                </tr>
                <tr>
                    <td>www.smsn.com</td>
                    <td>username, password</td>
                    <td>Supports delivery reports, Does not support schedule sms</td>
                </tr>
                <tr>
                    <td>www.smsthemall.com</td>
                    <td>username, password</td>
                    <td>Supports delivery reports, Does not support schedule sms</td>
                </tr>
            </tbody>
        </table>

        <p>
        You can also add the optional parameter "mobile" to the parameters list.<br/>
        By defining this parameter you can test the functionality of gateway being added.<br/>
        The "mobile" parameter value should be your mobile number in international form.<br/>
        e.g if your mobile is 69112233xx and you use a Greek mobile operator then you must<br/>
        prefix your mobile with "30" which is the international calling code for<br/>
        Greece. So the value of the mobile parameter should be 3069112233xx.<br/></p>

    </li>
    <li>Click submit.</li>
</ol>

<a name="gateways_edit">&nbsp;</a>
<h3>
    Edit gateway
    <a href="{$T_TEXTME_BASEURL}&cat=help#index">
        <img src="{$T_TEXTME_BASELINK}assets/images/16/arrow_up_blue.png" alt="Back to index" title="Back to index" />
    </a>
</h3>

<p>See add gateway above.</p>

<a name="gateways_delete">&nbsp;</a>
<h3>
    Delete gateway
    <a href="{$T_TEXTME_BASEURL}&cat=help#index">
        <img src="{$T_TEXTME_BASELINK}assets/images/16/arrow_up_blue.png" alt="Back to index" title="Back to index" />
    </a>
</h3>

<ol>
    <li>Click on "TextMe" icon from the "Control Center" panel</li>
    <li>Click on "Sms Gateways" tab.</li>
    <li>Click on "Delete gateway" icon.</li>
</ol>

<a name="gateways_test">&nbsp;</a>
<h3>
    Test gateway
    <a href="{$T_TEXTME_BASEURL}&cat=help#index">
        <img src="{$T_TEXTME_BASELINK}assets/images/16/arrow_up_blue.png" alt="Back to index" title="Back to index" />
    </a>
</h3>

<p>
    With this function you can test the functionality of a gateway.<br/>
    When using this function a test message will be sent to your mobile<br/>
    indicating you have configured TextMe properly for this gateway.
</p>

<ol>
    <li>Click on "Sms Gateways" tab.</li>
    <li>Click on "Test gateway" icon. This option is only available if you have set the "mobile" parameter for this gateway. (see Add gateway)</li>
</ol>

<a name="gateways_activate">&nbsp;</a>
<h3>
    Activate gateway
    <a href="{$T_TEXTME_BASEURL}&cat=help#index">
        <img src="{$T_TEXTME_BASELINK}assets/images/16/arrow_up_blue.png" alt="Back to index" title="Back to index" />
    </a>
</h3>

<ol>
    <li>Click on "TextMe" icon from the "Control Center" panel</li>
    <li>Click on "Sms Gateways" tab.</li>
    <li>Click on "Activate/Deactivate" icon.</li>
</ol>
