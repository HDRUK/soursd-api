<?php

namespace Database\Seeders;

use Hdruk\LaravelMjml\Models\EmailTemplate;
use Illuminate\Database\Seeder;

class EmailTemplatesSeeder extends Seeder
{
    private $mjmlHead = '
    <mj-head>
      <mj-html-attributes>
        <mj-html-attribute class="easy-email" multiple-attributes="false" attribute-name="text-color" text-color="#000000"></mj-html-attribute>
        <mj-html-attribute class="easy-email" multiple-attributes="false" attribute-name="font-family" font-family="-apple-system, BlinkMacSystemFont, \'Segoe UI\', \'Roboto\', \'Oxygen\', \'Ubuntu\', \'Cantarell\', \'Fira Sans\', \'Droid Sans\',\'Helvetica Neue\', sans-serif"></mj-html-attribute>
        <mj-html-attribute class="easy-email" multiple-attributes="false" attribute-name="font-size" font-size="14px"></mj-html-attribute>
        <mj-html-attribute class="easy-email" multiple-attributes="false" attribute-name="line-height" line-height="1.7"></mj-html-attribute>
        <mj-html-attribute class="easy-email" multiple-attributes="false" attribute-name="font-weight" font-weight="400"></mj-html-attribute>
        <mj-html-attribute class="easy-email" multiple-attributes="false" attribute-name="responsive" responsive="true"></mj-html-attribute>
      </mj-html-attributes>
      <mj-breakpoint width="480px" />
      <mj-attributes>
        <mj-text font-size="14px" />
        <mj-text line-height="1.7" />
        <mj-text font-weight="400" />
        <mj-all font-family="-apple-system, BlinkMacSystemFont, \'Segoe UI\', \'Roboto\', \'Oxygen\', \'Ubuntu\', \'Cantarell\', \'Fira Sans\', \'Droid Sans\',\'Helvetica Neue\', sans-serif" />
        <mj-text font-size="14px" />
        <mj-text color="#000000" />
        <mj-text line-height="1.7" />
        <mj-text font-weight="400" />
      </mj-attributes>
    </mj-head>
  ';

    private $titleBar = '
    <mj-wrapper padding="20px 0px 20px 0px" border="none" direction="ltr" text-align="center" >
      <mj-section background-repeat="repeat" background-size="auto" background-position="top center" border="none" direction="ltr" text-align="left" padding="0px 0px 0px 0px" >
        <mj-column border="none" vertical-align="top" padding="0px 0px 0px 0px" >
          <mj-image align="center" height="100px" src="[[env(BANNER_URL)]]" width="800px" padding="0px 0px 0px 0px" ></mj-image>
        </mj-column>
      </mj-section>
    </mj-wrapper>
    <mj-section background-repeat="repeat" background-size="auto" background-position="top center" border="none" direction="ltr" text-align="left" padding="0px 0px 0px 0px" >
      <mj-column border="none" vertical-align="top" padding="0px 0px 0px 0px" >
        <mj-spacer height="20px" ></mj-spacer>
      </mj-column>
    </mj-section>
  ';

    private $supportFooter = '
    <div>
        Please note, if you encounter any issue whilst registering you can request help by emailing [[env(SUPPORT_EMAIL)]].<br/><br/>Thanks!<br/>[[env(APP_NAME)]] Team.<br/>                         
    </div>
  ';

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $truncate = !env('DISABLE_TRUNCATE', false);

        if($truncate){
          EmailTemplate::truncate();
        }

        $templates = [
        [
            'identifier' => 'example_template',
            'subject' => 'Example Template',
            'body' => '<mjml>
            <mj-body>
              <mj-raw>
                <!-- Company Header -->
              </mj-raw>
              <mj-section background-color="#f0f0f0">
                <mj-column>
                  <mj-text font-style="italic" font-size="20px" color="#626262">Health Data Research UK</mj-text>
                </mj-column>
              </mj-section>
              <mj-raw>
                <!-- Image Header -->
              </mj-raw>
              <mj-section background-url="https://place-hold.it/600x100/000000/ffffff/grey.png" background-size="cover" background-repeat="no-repeat">
                <mj-column width="600px">
                  <mj-text align="center" color="#fff" font-size="40px" font-family="Helvetica Neue">[[HEADER_TEXT]]</mj-text>
                </mj-column>
              </mj-section>
              <mj-raw>
                <!-- Intro text -->
              </mj-raw>
              <mj-section background-color="#fafafa">
                <mj-column width="400px">
                  <mj-text font-style="italic" font-size="20px" font-family="Helvetica Neue" color="#626262">[[SUBHEADING_TEXT]]</mj-text>
                  <mj-text color="#525252">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin rutrum enim eget magna efficitur, eu semper augue semper. Aliquam erat volutpat. Cras id dui lectus. Vestibulum sed finibus lectus, sit amet suscipit nibh. Proin nec commodo purus.
                    Sed eget nulla elit. Nulla aliquet mollis faucibus.</mj-text>
                  <mj-button background-color="#F45E43" href="[[BUTTON_1_URL]]">Button 1 Text</mj-button>
                  <mj-button background-color="#F45E43" href="[[BUTTON_2_URL]]">Button 2 Text</mj-button>
                </mj-column>
              </mj-section>
              <mj-raw>
                <!-- Side image -->
              </mj-raw>
              <mj-section background-color="white">
                <mj-raw>
                  <!-- Left image -->
                </mj-raw>
                <mj-column>
                  <mj-image width="200px" src="https://place-hold.it/200x300/000000/ffffff/grey.png"></mj-image>
                </mj-column>
                <mj-raw>
                  <!-- right paragraph -->
                </mj-raw>
                <mj-column>
                  <mj-text font-style="italic" font-size="20px" font-family="Helvetica Neue" color="#626262">[[SUBHEADING_TEXT]]</mj-text>
                  <mj-text color="#525252">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin rutrum enim eget magna efficitur, eu semper augue semper. Aliquam erat volutpat. Cras id dui lectus. Vestibulum sed finibus lectus.</mj-text>
                </mj-column>
              </mj-section>
            </mj-body>
            </mjml>',
              'buttons' => '
              {
                "replacements": [
                        {
                            "placeholder": "[[BUTTON_1_URL]]",
                            "actual": "https://test.com/something1"
                        },
                        {
                            "placeholder": "[[BUTTON_2_URL]]",
                            "actual": "https://test.com/something2"
                        }
                    ]
                }
              ',
        ],
        [ 
          'identifier' => 'custodian_invite',
          'subject' => 'You\'ve been selected as a trusted Data Custodian!',
          'body' => '
            <mjml>
              '. $this->mjmlHead . '
              <mj-body background-color="#efeeea" width="600px" >
                ' . $this->titleBar . '
                <mj-wrapper border="none" direction="ltr" text-align="center" padding="20px 0px 20px 0px" >
                  <mj-section background-repeat="repeat" background-size="auto" background-position="top center" border="none" direction="ltr" text-align="left" padding="0px 0px 0px 0px" >
                    <mj-column border="none" vertical-align="top" padding="0px 0px 0px 0px" >
                      <mj-text align="left" padding="10px 25px 10px 25px" >
                        [[custodian.name]]<br><br>You\'ve been invited to sign-up as a trusted Data Custodian, for the [[env(APP_NAME)]] Registry system. To begin your sign-up process, please 
                        click the button below.
                        <div><br></div>
                        ' . $this->supportFooter . '
                      </mj-text>
                    </mj-column>
                  </mj-section>
                  <mj-section background-repeat="repeat" background-size="auto" background-position="top center" border="none" direction="ltr" text-align="left" padding="0px 0px 0px 0px" >
                    <mj-column border="none" vertical-align="top" padding="0px 0px 0px 0px" >
                      <mj-button align="center" background-color="#bd10e0" color="#ffffff" font-weight="normal" border-radius="3px" line-height="120%" target="_blank" vertical-align="middle" border="none" text-align="center" href="[[env(PORTAL_URL)]]/[[env(PORTAL_PATH_INVITE)]]" padding="10px 25px 10px 25px" >
                        Sign me up!
                      </mj-button>
                    </mj-column>
                  </mj-section>
                </mj-wrapper>
              </mj-body>
            </mjml >',
          'buttons' => '',
        ],
        [
          'identifier' => 'organisation_invite',
          'subject' => 'You\'ve been selected as a trusted Organisation!',
          'body' => '
            <mjml>
              '. $this->mjmlHead . '
              <mj-body background-color="#efeeea" width="600px" >
                ' . $this->titleBar . '
                <mj-wrapper border="none" direction="ltr" text-align="center" padding="20px 0px 20px 0px" >
                  <mj-section background-repeat="repeat" background-size="auto" background-position="top center" border="none" direction="ltr" text-align="left" padding="0px 0px 0px 0px" >
                    <mj-column border="none" vertical-align="top" padding="0px 0px 0px 0px" >
                      <mj-text align="left" padding="10px 25px 10px 25px" >
                        [[organisation.organisation_name]]<br><br>You\'ve been invited to sign-up as a trusted Organisation, for the [[env(APP_NAME)]] Registry system. To begin your sign-up process, please 
                        click the button below.
                        <div><br></div>
                        ' . $this->supportFooter . '
                      </mj-text>
                    </mj-column>
                  </mj-section>
                  <mj-section background-repeat="repeat" background-size="auto" background-position="top center" border="none" direction="ltr" text-align="left" padding="0px 0px 0px 0px" >
                    <mj-column border="none" vertical-align="top" padding="0px 0px 0px 0px" >
                      <mj-button align="center" background-color="#bd10e0" color="#ffffff" font-weight="normal" border-radius="3px" line-height="120%" target="_blank" vertical-align="middle" border="none" text-align="center" href="[[env(PORTAL_URL)]]/[[env(PORTAL_PATH_INVITE)]]" padding="10px 25px 10px 25px" >
                        Sign me up!
                      </mj-button>
                    </mj-column>
                  </mj-section>
                </mj-wrapper>
              </mj-body>
            </mjml >',
          'buttons' => '',
        ],
        [
          'identifier' => 'custodian_user_invite',
          'subject' => 'You\'ve been invited to join the Researcher Registry',
          'body' => '
            <mjml>
              '. $this->mjmlHead . '
              <mj-body background-color="#efeeea" width="600px">
                ' . $this->titleBar . '
                <mj-wrapper border="none" direction="ltr" text-align="center" padding="20px 0px 20px 0px">
                    <mj-section background-repeat="repeat" background-size="auto" background-position="top center" border="none" direction="ltr" text-align="left" padding="0px 0px 0px 0px">
                      <mj-column border="none" vertical-align="top" padding="0px 0px 0px 0px">
                          <mj-text align="left" padding="10px 25px 10px 25px">
                            You\'ve been added [[role.description]] by [[custodian.name]].                     
                            <div><br/></div>          
                            ' . $this->supportFooter . '
                          </mj-text>
                      </mj-column>
                    </mj-section>
                    <mj-section background-repeat="repeat" background-size="auto" background-position="top center" border="none" direction="ltr" text-align="left" padding="0px 0px 0px 0px" >
                      <mj-column border="none" vertical-align="top" padding="0px 0px 0px 0px" >
                        <mj-button align="center" background-color="#bd10e0" color="#ffffff" font-weight="normal" border-radius="3px" line-height="120%" target="_blank" vertical-align="middle" border="none" text-align="center" href="[[env(PORTAL_URL)]]/[[env(PORTAL_PATH_INVITE)]]" padding="10px 25px 10px 25px" >
                          Sign me up!
                        </mj-button>
                      </mj-column>
                    </mj-section>
                </mj-wrapper>
              </mj-body>
            </mjml>
          ',
          'buttons' => '',
        ],
        [
            'identifier' => 'user_otp',
            'subject' => 'Confirm your Registry Email address',
            'body' => '
          <mjml>
          '. $this->mjmlHead . '
          <mj-body background-color="#efeeea" width="600px" ><mj-wrapper padding="20px 0px 20px 0px" border="none" direction="ltr" text-align="center" ><mj-section padding="0px" text-align="left" ><mj-column ><mj-image align="center" height="auto" padding="0px 0px 0px 0px" src="https://fakeimg.pl/800x200?text=[[env(APP_NAME)]]+OTP" ></mj-image></mj-column></mj-section></mj-wrapper><mj-section padding="0px" text-align="left" ><mj-column ><mj-spacer height="20px" padding="   " ></mj-spacer></mj-column></mj-section><mj-section padding="0px" text-align="left" ><mj-column ><mj-text padding="10px 25px 10px 25px" align="left" font-size="16px" font-weight="bold" >Confirm your email address</mj-text></mj-column></mj-section><mj-section padding="0px" text-align="left" ><mj-column ><mj-spacer height="20px" padding="   " ></mj-spacer></mj-column></mj-section><mj-section padding="0px" text-align="left" ><mj-column ><mj-text padding="10px 25px 10px 25px" align="left" >To verify your email address, please enter the code below into your web browser.</mj-text></mj-column></mj-section><mj-section padding="0px" text-align="left" ><mj-column ><mj-spacer height="20px" padding="   " ></mj-spacer></mj-column></mj-section><mj-section padding="0px" text-align="left" ><mj-column ><mj-text padding="10px 25px 10px 25px" align="center" font-size="24px" font-style="normal" container-background-color="#000000" color="#FFFFFF" >[[users.otp]]</mj-text></mj-column></mj-section><mj-section padding="0px" text-align="left" ><mj-column ><mj-text padding="10px 25px 10px 25px" align="left" >The above code is only valid for [[env(OTP_VALIDITY_MINUTES)]] minute(s). <br><br>If you didn\'t request this email, worry not, you can safely ignore it.</mj-text></mj-column></mj-section><mj-section padding="0px" text-align="left" ><mj-column ><mj-spacer height="20px" padding="   " ></mj-spacer></mj-column></mj-section><mj-section padding="0px   " text-align="left" ><mj-column ><mj-text padding="10px 25px 10px 25px" align="left" >[[env(APP_NAME)]] Support Team<div>[[env(SUPPORT_EMAIL)]]</div></mj-text></mj-column></mj-section></mj-body></mjml > 
          ',
            'buttons' => '',
        ],
        [
          'identifier' => 'researcher_invite',
          'subject' => 'You\'ve been invited to join the Researcher Registry',
          'body' => '
            <mjml>
              '. $this->mjmlHead . '
              <mj-body background-color="#efeeea" width="600px" >
                <mj-body background-color="#efeeea" width="600px" >
                  ' . $this->titleBar . '
                  <mj-wrapper border="none" direction="ltr" text-align="center" padding="20px 0px 20px 0px" >
                    <mj-section background-repeat="repeat" background-size="auto" background-position="top center" border="none" direction="ltr" text-align="left" padding="0px 0px 0px 0px" >
                      <mj-column border="none" vertical-align="top" padding="0px 0px 0px 0px" >
                        <mj-text align="left" padding="10px 25px 10px 25px" >
                          [[users.first_name]] [[users.last_name]]<br><br>You\'ve been invited to sign-up as a Researcher within the [[env(APP_NAME)]] Registry system, by [[organisations.organisation_name]]. To begin your sign-up process, please 
                          click the button below.
                          <div><br></div>
                          ' . $this->supportFooter . '
                          <div><br></div>
                          <div><br></div>
                          </div>
                        </mj-text>
                      </mj-column>
                    </mj-section>
                    <mj-section background-repeat="repeat" background-size="auto" background-position="top center" border="none" direction="ltr" text-align="left" padding="0px 0px 0px 0px" >
                      <mj-column border="none" vertical-align="top" padding="0px 0px 0px 0px" >
                        <mj-button align="center" background-color="#bd10e0" color="#ffffff" font-weight="normal" border-radius="3px" line-height="120%" target="_blank" vertical-align="middle" border="none" text-align="center" href="[[env(PORTAL_URL)]]/[[env(PORTAL_PATH_INVITE)]]" padding="10px 25px 10px 25px" >Sign me up!</mj-button>
                      </mj-column>
                    </mj-section>
                  </mj-wrapper>
                </mj-body>
              </mjml > 
            ',
            'buttons' => '',
        ],
        [
          'identifier' => 'researcher_without_organisation_invite',
          'subject' => 'You\'ve been invited to join the Researcher Registry',
          'body' => '
            <mjml>
              '. $this->mjmlHead . '
              <mj-body background-color="#efeeea" width="600px" >
                <mj-body background-color="#efeeea" width="600px" >
                  ' . $this->titleBar . '
                  <mj-wrapper border="none" direction="ltr" text-align="center" padding="20px 0px 20px 0px" >
                    <mj-section background-repeat="repeat" background-size="auto" background-position="top center" border="none" direction="ltr" text-align="left" padding="0px 0px 0px 0px" >
                      <mj-column border="none" vertical-align="top" padding="0px 0px 0px 0px" >
                        <mj-text align="left" padding="10px 25px 10px 25px" >
                          [[users.first_name]] [[users.last_name]]<br><br>You\'ve been invited to sign-up as a Researcher within the [[env(APP_NAME)]] Registry system. To begin your sign-up process, please 
                          click the button below.
                          <div><br></div>
                          ' . $this->supportFooter . '
                          <div><br></div>
                          <div><br></div>
                          </div>
                        </mj-text>
                      </mj-column>
                    </mj-section>
                    <mj-section background-repeat="repeat" background-size="auto" background-position="top center" border="none" direction="ltr" text-align="left" padding="0px 0px 0px 0px" >
                      <mj-column border="none" vertical-align="top" padding="0px 0px 0px 0px" >
                        <mj-button align="center" background-color="#bd10e0" color="#ffffff" font-weight="normal" border-radius="3px" line-height="120%" target="_blank" vertical-align="middle" border="none" text-align="center" href="[[env(PORTAL_URL)]]/[[env(PORTAL_PATH_INVITE)]]" padding="10px 25px 10px 25px" >Sign me up!</mj-button>
                      </mj-column>
                    </mj-section>
                  </mj-wrapper>
                </mj-body>
              </mjml > 
            ',
            'buttons' => '',
        ],
        [
          'identifier' => 'delegate_invite',
          'subject' => 'You\'ve been invited to join the Researcher Registry',
          'body' => '
            <mjml>
              '. $this->mjmlHead . '
              <mj-body background-color="#efeeea" width="600px" >
                <mj-body background-color="#efeeea" width="600px" >
                  ' . $this->titleBar . '
                  <mj-wrapper border="none" direction="ltr" text-align="center" padding="20px 0px 20px 0px" >
                    <mj-section background-repeat="repeat" background-size="auto" background-position="top center" border="none" direction="ltr" text-align="left" padding="0px 0px 0px 0px" >
                      <mj-column border="none" vertical-align="top" padding="0px 0px 0px 0px" >
                        <mj-text align="left" padding="10px 25px 10px 25px" >
                          [[users.first_name]] [[users.last_name]]<br><br>You\'ve been invited to sign-up as a delegate user within the [[env(APP_NAME)]] Registry system, by [[organisations.organisation_name]]. To begin your sign-up process, please 
                          click the button below.
                          <div><br></div>
                          ' . $this->supportFooter . '
                          <div><br></div>
                          <div><br></div>
                          </div>
                        </mj-text>
                      </mj-column>
                    </mj-section>
                    <mj-section background-repeat="repeat" background-size="auto" background-position="top center" border="none" direction="ltr" text-align="left" padding="0px 0px 0px 0px" >
                      <mj-column border="none" vertical-align="top" padding="0px 0px 0px 0px" >
                        <mj-button align="center" background-color="#bd10e0" color="#ffffff" font-weight="normal" border-radius="3px" line-height="120%" target="_blank" vertical-align="middle" border="none" text-align="center" href="[[env(PORTAL_URL)]]/user/invited/[[users.id]]/[[organisations.id]]" padding="10px 25px 10px 25px" >Sign me up!</mj-button>
                      </mj-column>
                    </mj-section>
                  </mj-wrapper>
                </mj-body>
              </mjml > 
            ',
            'buttons' => '',
        ],
        [
            'identifier' => 'delegate_sponsor',
            'subject' => 'You\'re asked to verify a researcher you employ!',
            'body' => '
          <mjml>
            '. $this->mjmlHead . '
            <mj-body background-color="#efeeea" width="600px">
              ' . $this->titleBar . '
              <mj-wrapper border="none" direction="ltr" text-align="center" padding="20px 0px 20px 0px">
                <mj-section background-repeat="repeat" background-size="auto" background-position="top center" border="none" direction="ltr" text-align="left" padding="0px 0px 0px 0px">
                  <mj-column border="none" vertical-align="top" padding="0px 0px 0px 0px">
                    <mj-text align="left" padding="10px 25px 10px 25px">[[delegate_first_name]] [[delegate_last_name]]<br/><br/>
                    As a delegate for [[organisation_name]] on the [[env(APP_NAME)]] Registry system. You are requested to verify 
                    a recent researcher registration as someone who is currently employed by [[organisation_name]]. We ask that you please confirm this to be true, by clicking the button below. That\'s all. The rest is automatic!
                    <div><br/></div>
                    <div>
                      Name: [[user_first_name]] [[user_last_name]]<br/>
                      Registered: [[user_created_at]] <br/>
                    </div><br/>
                      ' . $this->supportFooter . '
                        <div><br></div>
                        <div><br></div>
                      </div>
                    </mj-text>
                  </mj-column>
                </mj-section>
                <mj-section background-repeat="repeat" background-size="auto" background-position="top center" border="none" direction="ltr" text-align="left" padding="0px 0px 0px 0px">
                  <mj-column border="none" vertical-align="top" padding="0px 0px 0px 0px">
                    <mj-button align="center" background-color="#bd10e0" color="#ffffff" font-weight="normal" border-radius="3px" line-height="120%" target="_blank" vertical-align="middle" border="none" text-align="center" href="[[env(PORTAL_URL)]]" padding="10px 25px 10px 25px">I confirm that the named Researcher above is employed by [[organisation_name]]!</mj-button>
                  </mj-column>
                </mj-section>
              </mj-wrapper>
            </mj-body>
          </mjml>
          ',
          'buttons' => '',
        ],
        [
          'identifier' => 'pro_email_verify',
          'subject' => 'Confirm your professional email address',
          'body' => '
            <mjml>
              '. $this->mjmlHead . '
                <mj-body background-color="#efeeea" width="600px" >
                <mj-wrapper padding="20px 0px 20px 0px" border="none" direction="ltr" text-align="center" >
                    <mj-section padding="0px" text-align="left" >
                        <mj-column >
                            <mj-image align="center" height="auto" padding="0px 0px 0px 0px" src="https://fakeimg.pl/800x200?text=[[env(APP_NAME)]]+Pro+Email" ></mj-image>
                        </mj-column>
                    </mj-section>
                </mj-wrapper>
                <mj-section padding="0px" text-align="left" >
                    <mj-column >
                        <mj-spacer height="20px" padding="5px" ></mj-spacer>
                    </mj-column>
                </mj-section>
                <mj-section padding="0px" text-align="left" >
                    <mj-column >
                        <mj-text padding="10px 25px 10px 25px" align="left" font-size="16px" font-weight="bold" >Confirm your email address</mj-text>
                    </mj-column>
                </mj-section>
                <mj-section padding="0px" text-align="left" >
                <mj-column >
                    <mj-spacer height="20px" padding="5px" ></mj-spacer>
                </mj-column>
            </mj-section>
            <mj-section padding="0px" text-align="left" >
            <mj-column >
                <mj-text padding="10px 25px 10px 25px" align="left" >Hi [[users.first_name]]<br/>To verify your recently added professional email address, please click the button below.</mj-text>
            </mj-column>
            </mj-section>
            <mj-section padding="0px" text-align="left" ><mj-column >
            <mj-spacer height="20px" padding="10px" ></mj-spacer></mj-column></mj-section>
            <mj-section padding="0px" text-align="left" >
                <mj-column >
                <mj-button align="center" background-color="#bd10e0" color="#ffffff" font-weight="normal" border-radius="3px" line-height="120%" target="_blank" vertical-align="middle" border="none" text-align="center" href="[[env(PORTAL_URL)]]" padding="10px 25px 10px 25px">Verify Email</mj-button>
                </mj-column>
            </mj-section>
            <mj-section padding="0px" text-align="left" >
                <mj-column >
                    <mj-spacer height="20px" padding="5px" ></mj-spacer>
                </mj-column>
            </mj-section>
            <mj-section padding="0px" text-align="left" >
                <mj-column >
                    <mj-text padding="10px 25px 10px 25px" align="left" >[[env(APP_NAME)]] Support Team<div>[[env(SUPPORT_EMAIL)]]</div></mj-text>
                </mj-column>
            </mj-section>
            </mj-body></mjml >
              ',
              'buttons' => '',
        ]
        ];

        foreach ($templates as $template) {
          EmailTemplate::updateOrCreate(
              ['identifier' => $template['identifier']],
              [
                  'subject' => $template['subject'],
                  'body' => $template['body'],
                  'buttons' => $template['buttons'] ?? '',
              ]
          );
      }


    }
}
