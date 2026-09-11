## [1.36.0](https://github.com/HDRUK/safepeopleregistry-api/compare/v1.35.0...v1.36.0) (2026-09-11)

### ✨ Features

* **REGISTRY-000:** Implement deployment-step mechanism exactly as in Gateway (#781) ([e18228e](https://github.com/HDRUK/safepeopleregistry-api/commit/e18228e9079be030fdad865718e1a842131b385a))
* **REGISTRY-000:** remove EntityModels (#783) ([6f2da4c](https://github.com/HDRUK/safepeopleregistry-api/commit/6f2da4c5e257bd52553413675724a5489a00091a))
* **REGISTRY-000:** Rename CustodianModelConfig.entity_model_id to decision_model_id (#785) ([23d6802](https://github.com/HDRUK/safepeopleregistry-api/commit/23d6802d7ed4214e22fbfdbf314d4e639c790a3b))
* **REGISTRY-000:** Rename entity_model_type to decision_model_type (#786) ([38586ee](https://github.com/HDRUK/safepeopleregistry-api/commit/38586eea6aab0b29e6efc86669aad9eed51c64ea))
* **REGISTRY-2745:** Add policy/gating to CustodianModelConfigController (#793) ([d9a4b0d](https://github.com/HDRUK/safepeopleregistry-api/commit/d9a4b0df969fdc2ba42bfb62dbcf3f2a5265f8f4))
* **REGISTRY-2906:** Capture the first name and last name from the IDVT document (#784) ([6c72baf](https://github.com/HDRUK/safepeopleregistry-api/commit/6c72baf8c7527139a1bc26b8877d6bafc906b06b))
* **REGISTRY-2913:** Enables SSO for gateway (#787) ([24be564](https://github.com/HDRUK/safepeopleregistry-api/commit/24be5645d7f1fdafde588df347b1513cdeb27e10))
* **REGISTRY-2917:** Adds SAML SSO to Registry (#788) ([f492b2d](https://github.com/HDRUK/safepeopleregistry-api/commit/f492b2d9f7285a2616045832a824b0b341a90b2d))
* **REGISTRY-2918:** Implements account linking via social providers (#789) ([6913f98](https://github.com/HDRUK/safepeopleregistry-api/commit/6913f989136e6b41174de8c1195388a33b71298c))

### 🐛 Bug Fixes

* **REGISTRY-2281:** Fix delegate contact rule  (#782) ([4f15abd](https://github.com/HDRUK/safepeopleregistry-api/commit/4f15abd8f4a4e80702f7c7447460cceaf7ca9080))
* **REGISTRY-2281:** Security compliance flags for Orgs (#791) ([ff2a238](https://github.com/HDRUK/safepeopleregistry-api/commit/ff2a238c26d2cb487606472ba1dea471f26a8786))
* **REGISTRY-2496:** Automated flags config not reflected (#780) ([339acdf](https://github.com/HDRUK/safepeopleregistry-api/commit/339acdf73bdf79e4ea6b20d7a50a5dcb152e03e1))
* **REGISTRY-2743:** Fix SRO Declaration Form upload notification bug (#795) ([f54b3dd](https://github.com/HDRUK/safepeopleregistry-api/commit/f54b3dd517b50886b1de74716c3af73591e1fb67))
* **REGISTRY-2756:** CustodianUsers: Fix permissions via policy (#792) ([29d8c66](https://github.com/HDRUK/safepeopleregistry-api/commit/29d8c66e1d1c9c89edfe5d5367130946d3775108))
* **REGISTRY-2842:** Update Organisation home actions page for the Delegate view (#790) ([89051f2](https://github.com/HDRUK/safepeopleregistry-api/commit/89051f2234a4e0c6329fbd16b6618eed64007f7e))
* **REGISTRY-2946:** Fix tests that incorrectly use nonexistent ids (#794) ([10038e7](https://github.com/HDRUK/safepeopleregistry-api/commit/10038e757fe0fb28950922ddbb349faad5ea9582))

## [1.35.0](https://github.com/HDRUK/safepeopleregistry-api/compare/v1.34.0...v1.35.0) (2026-08-18)

### ✨ Features

* **REGISTRY-2862:** query user project validation status (#769) ([98af75a](https://github.com/HDRUK/safepeopleregistry-api/commit/98af75a841cbc6ff928bfd3bbedceb024cf5e8d0))
* **REGISTRY-2890:** #3 - Massive amount of changes to fix up swagger… (#774) ([01c4370](https://github.com/HDRUK/safepeopleregistry-api/commit/01c437059225136712585eae16c29ed984ba32c3))
* **REGISTRY-2890:** Fill gaps in openapi swagger annotations in orde… (#771) ([a441eb3](https://github.com/HDRUK/safepeopleregistry-api/commit/a441eb3ecdb34534134a972889d9adbe8def501c))
* **REGISTRY-2890:** fix php version for composer error (#772) ([e215448](https://github.com/HDRUK/safepeopleregistry-api/commit/e21544810cce57635d085e912c7a4dfea036bf44))
* **REGISTRY-2899:** Prevent changing affiliation email (#777) ([d9a29f0](https://github.com/HDRUK/safepeopleregistry-api/commit/d9a29f0abdfdfe0640ed069a91d055a2c3002a0c))

### 🐛 Bug Fixes

* **REGISTRY-2772:** Protected handling of `system_approved` field (#729) ([d4b2a1f](https://github.com/HDRUK/safepeopleregistry-api/commit/d4b2a1f93c5ffd1ce4cdbbc8cc27869fa2ef14e3))
* **REGISTRY-2772:** Revert removal of Organisation IDVT (#767) ([c809502](https://github.com/HDRUK/safepeopleregistry-api/commit/c8095024437607d24be73698e3e7873b3ac1a5cd))
* **REGISTRY-2864:** Tidy user query content (#775) ([30616d0](https://github.com/HDRUK/safepeopleregistry-api/commit/30616d078790b40eefbbcca616d1c1e2db698e5b))
* **REGISTRY-2882:** Don't unapprove an Organisation when it uploads a file (#778) ([ed14c34](https://github.com/HDRUK/safepeopleregistry-api/commit/ed14c348fb0f2775f59d77a981f877619bf6c231))

## [1.34.0](https://github.com/HDRUK/safepeopleregistry-api/compare/v1.33.0...v1.34.0) (2026-07-31)

### ✨ Features

* **REGISTRY-2843:** Remove Read Requests (#761) ([58f4527](https://github.com/HDRUK/safepeopleregistry-api/commit/58f4527725146bbe4bde890820432a09d681a7b2))

### 🐛 Bug Fixes

* **GAT-9140:** added condition to take cloud-db backup before the release (#764) ([98137bd](https://github.com/HDRUK/safepeopleregistry-api/commit/98137bd62d3cfa39ee8490aca4eea3bb46d30350)), closes [GAT-9140](undefinedGAT-9140)
* **REGISTRY-000:** Update test user keycloak ID (#763) ([e961ac7](https://github.com/HDRUK/safepeopleregistry-api/commit/e961ac76ae53dddaa0fab95996f4b737e40e764e))
* **REGISTRY-1006:** Organisations - add parent organisation (subsidiaries) (#765) ([6bec762](https://github.com/HDRUK/safepeopleregistry-api/commit/6bec7628fd4d3bf64731f9a1bec42a8d410de3b0))

## [1.33.0](https://github.com/HDRUK/safepeopleregistry-api/compare/v1.32.1...v1.33.0) (2026-07-10)

### ✨ Features

* **REGISTRY-000:** Remove RegistryHasEmployments (#757) ([09e7632](https://github.com/HDRUK/safepeopleregistry-api/commit/09e763245d70ad79da01434f071f11a58a24d043))
* **REGISTRY-2619:** Update Accreditations model to be used for Accredited Researcher Registrations (#755) ([dfff62b](https://github.com/HDRUK/safepeopleregistry-api/commit/dfff62bbe33afceef9c57f435c460bfae7c6ae80))
* **REGISTRY-2811:** Seeders tidyup (#756) ([00bc00a](https://github.com/HDRUK/safepeopleregistry-api/commit/00bc00acef38e79b09fda0bdff14852c27924b71))
* **REGISTRY-2845:** Fix validation checks observer (#760) ([d22d5e9](https://github.com/HDRUK/safepeopleregistry-api/commit/d22d5e95734a50636d8b3b9f05a557eaae0e71c5))

### 🐛 Bug Fixes

* **GAT-9056:** added pipeline for automated Jira release to the registry apps (#751) ([3f9ab1b](https://github.com/HDRUK/safepeopleregistry-api/commit/3f9ab1b90eace9cd4f209b26063805658179863e)), closes [GAT-9056](undefinedGAT-9056)
* **REGISTRY-0000:** fix CI failures (#754) ([ff5f8dd](https://github.com/HDRUK/safepeopleregistry-api/commit/ff5f8dd051377bb68b14be0737c0d0b493d6b374))
* **REGISTRY-0000:** Fix naming of facade to meet psr-4 autoloading standard (#752) ([7d851e2](https://github.com/HDRUK/safepeopleregistry-api/commit/7d851e2ad6150c3e3e569603048dca842ef55eba))
* **REGISTRY-000:** Fix custodian_user_id / custodian_id confusion (#758) ([a05cf9f](https://github.com/HDRUK/safepeopleregistry-api/commit/a05cf9f572d1621bffe5d5f80bc5cbf26d166383))
* **REGISTRY-000:** Fix linting issues (#759) ([d551bbb](https://github.com/HDRUK/safepeopleregistry-api/commit/d551bbb3caa2734e8e2bd5456e572e09d888103a))
* **REGISTRY-000:** Fixing the many mis-spellings of 'organisation' and other words in the codebase. (#753) ([7eff82b](https://github.com/HDRUK/safepeopleregistry-api/commit/7eff82b3f2a60654755e425c72c769f589c18dcc))
* **REGISTRY-2776:** Add associated CustodianProjectOrganisation information to CustodianProjectUser entries in CustodianHasProjectUserController::index() (#750) ([07be9e5](https://github.com/HDRUK/safepeopleregistry-api/commit/07be9e5b31926a9eec60deb63c494a69492de7b9))

## [1.32.1](https://github.com/HDRUK/safepeopleregistry-api/compare/v1.32.0...v1.32.1) (2026-06-25)

### 🐛 Bug Fixes

* **REGISTRY-2832:** Fix replacements in email subject lines (#748) ([26a322c](https://github.com/HDRUK/safepeopleregistry-api/commit/26a322cdf7f177f4367ac406504b119e5b6030d6))

## [1.32.0](https://github.com/HDRUK/safepeopleregistry-api/compare/v1.31.2...v1.32.0) (2026-06-23)

### ✨ Features

* **REGISTRY-2826:** Filter activity logs by custodian (#746) ([fed493f](https://github.com/HDRUK/safepeopleregistry-api/commit/fed493fc3f97c70018e785676aec145c60bd4c72))

## [1.31.2](https://github.com/HDRUK/safepeopleregistry-api/compare/v1.31.1...v1.31.2) (2026-06-17)

### 🐛 Bug Fixes

* **GAT-8809:** updated semantic pipeline (#738) ([0068dd6](https://github.com/HDRUK/safepeopleregistry-api/commit/0068dd68615dcd0e7ad3aae5b9da1118c2d5e14c)), closes [GAT-8809](undefinedGAT-8809)
* **REGISTRY-2791:** Fix ValidationLog issues and remove CustodianHasValidationCheck relationship (#740) ([fa08dff](https://github.com/HDRUK/safepeopleregistry-api/commit/fa08dffa106c80b4d506c20cdb908d504683af20))
* **REGISTRY-2791:** Update to make query more specific  (#741) ([29cd50f](https://github.com/HDRUK/safepeopleregistry-api/commit/29cd50fc86b5c30651999af74327c24ae560d616))

## [1.31.1](https://github.com/HDRUK/safepeopleregistry-api/compare/v1.31.0...v1.31.1) (2026-05-29)

### 🐛 Bug Fixes

* **REGISTRY-0000:** Removes duplication of validation_logs on new custodian creation. (#736) ([e132509](https://github.com/HDRUK/safepeopleregistry-api/commit/e132509029f2b3c24c45783671fba1d8a7b9b5e7))

## [1.31.0](https://github.com/HDRUK/safepeopleregistry-api/compare/v1.30.0...v1.31.0) (2026-05-29)

### ✨ Features

* **REGISTRY-2673:** Update default validation checks (#732) ([2ebe8c5](https://github.com/HDRUK/safepeopleregistry-api/commit/2ebe8c5324d84f34833ecbb14237a121b0e95fa4))
* **REGISTRY-2744:** Return permissions with custodian user (#722) ([5ee9406](https://github.com/HDRUK/safepeopleregistry-api/commit/5ee9406a1aa5571f6fba8032d78018c4510077f0))
* **REGISTRY-2774:** Add "People Services/Human Resources" to Departments (#730) ([b088a97](https://github.com/HDRUK/safepeopleregistry-api/commit/b088a975fc2d808aaf0428e3f987cf7ef776905c))
* **REGISTRY-2774:** Add HR department to existing Orgs (#731) ([8c2cd29](https://github.com/HDRUK/safepeopleregistry-api/commit/8c2cd292e1a74a85168012a20dbae4dd726697ec))

### 🐛 Bug Fixes

* **GAT-8809:** updated workflows to use reusable pipelines (#724) ([7c73757](https://github.com/HDRUK/safepeopleregistry-api/commit/7c73757f71ce1754daf89ff5251900ec3b6505b7)), closes [GAT-8809](undefinedGAT-8809)
* **REGISTRY-0000:** [ClaudeAI] Security audit. Including regression tests for vulnerability fixes. (#723) ([f480a1e](https://github.com/HDRUK/safepeopleregistry-api/commit/f480a1e33ff7bf4167dcfa741d1a92994abf6d8d))
* **REGISTRY-2771:** Make Organisation.companies_house_no nullable (#725) ([dd9e3d7](https://github.com/HDRUK/safepeopleregistry-api/commit/dd9e3d7ef39b203086cdc2fd8ef334bd589c72a5))
* **REGISTRY-2783:** Fix updating of emails (#733) ([887c61b](https://github.com/HDRUK/safepeopleregistry-api/commit/887c61b9ad3278d1bbe4fe07d18efa64f8158221))
* **REGISTRY-2787:** Fix static route being consumed by dynamic route (#734) ([b8ba1ec](https://github.com/HDRUK/safepeopleregistry-api/commit/b8ba1ecf5c119a0900ce522a65348ff551ee588e))

## [1.30.0](https://github.com/HDRUK/safepeopleregistry-api/compare/v1.29.0...v1.30.0) (2026-05-22)

### ✨ Features

* **REGISTRY-2160:** include invite status (#710) ([2abc57b](https://github.com/HDRUK/safepeopleregistry-api/commit/2abc57bf969e7baf2db8e202f41717770f2eaa04))
* **REGISTRY-2584:** only send confirm email when uploaded (#713) ([3604099](https://github.com/HDRUK/safepeopleregistry-api/commit/3604099e6a4eb0fb2df502c93f43c7bf25992d5c))
* **REGISTRY-2741:** Allow resend if org unclaimed (#716) ([786f08d](https://github.com/HDRUK/safepeopleregistry-api/commit/786f08dc0d38de19b1a1c512a9f697ef91219d32))

### 🐛 Bug Fixes

* **REGISTRY-0000:** Dev Test Users Keycloak IDs (#711) ([b1ba971](https://github.com/HDRUK/safepeopleregistry-api/commit/b1ba9719561c74e926a304394418bc2481d349df))
* **REGISTRY-0000:** Me lil tweaks (#718) ([6f9aa00](https://github.com/HDRUK/safepeopleregistry-api/commit/6f9aa00a12cb6ba184325005e284a5efb5768923))
* **REGISTRY-2687:** Actualy duplicate the mandatory checks (#719) ([f80e5c3](https://github.com/HDRUK/safepeopleregistry-api/commit/f80e5c3a5b1bf1e36e7ddf9c4f61eefdcc34ced6))
* **REGISTRY-2687:** Creating validation checks for custodians (#717) ([2cf87ce](https://github.com/HDRUK/safepeopleregistry-api/commit/2cf87cee3a99ea3e9d519f9263d7bf2550f2f5ab))
* **REGISTRY-2726:** Add "More Org info requested" to status list (#712) ([d932dd4](https://github.com/HDRUK/safepeopleregistry-api/commit/d932dd40380b12fd6d0807a561852df2d6b110e5))
* **REGISTRY-2727:** stop unapproving orgs (#714) ([4305525](https://github.com/HDRUK/safepeopleregistry-api/commit/430552594d8913e71ee61c0afe454fa8a675078b))
* **REGISTRY-2739:** Test email delay (#715) ([ee6026c](https://github.com/HDRUK/safepeopleregistry-api/commit/ee6026c0b9304699b066c531a6861755116e1480))

## [1.29.0](https://github.com/HDRUK/safepeopleregistry-api/compare/v1.28.0...v1.29.0) (2026-05-11)

### ✨ Features

* **GAT-7958:** added github authentication (#674) ([48836a6](https://github.com/HDRUK/safepeopleregistry-api/commit/48836a68512617ae06f13947a0a92bba7f1e7624)), closes [GAT-7958](GAT-7958)
* **GAT-7967:** updated semantic pipeline for merge back to dev (#687) ([2deb269](https://github.com/HDRUK/safepeopleregistry-api/commit/2deb26926dfd187e75b1d7d60462e703031d4181)), closes [GAT-7967](GAT-7967)
* **REGISTRY-2197:** org stastus on project users (#704) ([ef31222](https://github.com/HDRUK/safepeopleregistry-api/commit/ef312225e78187d990bc72594521b0f72629bca0))
* **REGISTRY-2361:** Organisation ico_expiry_evidence field (#680) ([38158ff](https://github.com/HDRUK/safepeopleregistry-api/commit/38158ff02e767d8817e3d7475dc386ab2e2f63b2))
* **REGISTRY-2361:** Organisation ICO, ODS, DSPTK fields (#676) ([4b1058f](https://github.com/HDRUK/safepeopleregistry-api/commit/4b1058f0e0db8e96991a431be041b4babc043e0a))
* **REGISTRY-2506:** Allow users to resend verication email for affiliation (#701) ([2908b09](https://github.com/HDRUK/safepeopleregistry-api/commit/2908b09a368bcaeb86d4a21d99fd35a6c4773001))
* **REGISTRY-2562:** add latest approval to project details (#697) ([55e1550](https://github.com/HDRUK/safepeopleregistry-api/commit/55e1550c0c66bd8f02d33ce292b0a1f6af533995))
* **REGISTRY-2562:** add latest approval to projects (#700) ([5420fd8](https://github.com/HDRUK/safepeopleregistry-api/commit/5420fd806c43fd9afaf7276431721410b65af850))
* **REGISTRY-2606:** Bypass verification needed where user email login is affiliation email (#702) ([5191528](https://github.com/HDRUK/safepeopleregistry-api/commit/519152865c1bf49f49af3da0a08c075f727348a8))
* **REGISTRY-2697:** Include org status (#707) ([24f99d9](https://github.com/HDRUK/safepeopleregistry-api/commit/24f99d9cb95b3bd40a2fa974bab40cd47202a780))

### 🐛 Bug Fixes

* **REGISTRY-0000:** emails without redis (#675) ([c8a27cf](https://github.com/HDRUK/safepeopleregistry-api/commit/c8a27cfde1374021a6c0d5576f62da12fa3c4fa0))
* **REGISTRY-0000:** Fix for uploads erroring (#678) ([08af6e3](https://github.com/HDRUK/safepeopleregistry-api/commit/08af6e3a90069247854e47b92fa09dc162018ba5))
* **REGISTRY-0000:** Missed State (#685) ([728dfbc](https://github.com/HDRUK/safepeopleregistry-api/commit/728dfbc21cc2a687f6e53987539bc2e937362b29))
* **REGISTRY-0000:** Status Edge case (#686) ([0e2c2f9](https://github.com/HDRUK/safepeopleregistry-api/commit/0e2c2f969af778edb6d42a3e191a6dc1f24ea4e0))
* **REGISTRY-1668:** validation status - 2 (#684) ([3b80e5d](https://github.com/HDRUK/safepeopleregistry-api/commit/3b80e5d88f12e17a7174969d57f889dcdd394780))
* **REGISTRY-1668:** validation status (#681) ([d5808ec](https://github.com/HDRUK/safepeopleregistry-api/commit/d5808ec649994994ee1b91e51d9bc4ccfecb12db))
* **REGISTRY-1882:** make postcode nullable to prevent veriff crash (#703) ([324e89c](https://github.com/HDRUK/safepeopleregistry-api/commit/324e89c39427a7e1bc2ea114ed83d0587db3d712))
* **REGISTRY-2165:** observer not running for affilation emails to trigger (#691) ([608cda2](https://github.com/HDRUK/safepeopleregistry-api/commit/608cda26b25ce0016aac14457621426cb9f26327))
* **REGISTRY-2566:** Org statuses (#679) ([2bde1d3](https://github.com/HDRUK/safepeopleregistry-api/commit/2bde1d3ae122c490bb0a915820cca99a32360c7a))
* **REGISTRY-2585:** Dropped login hint (#683) ([92f94d9](https://github.com/HDRUK/safepeopleregistry-api/commit/92f94d9bb37e55e3c2828224508096dd7f7dcbfa))
* **REGISTRY-2594:** No state on project creation (#698) ([d86c02c](https://github.com/HDRUK/safepeopleregistry-api/commit/d86c02c63d34f15a4aa0f5d4c53ddd3b3d4fc419))
* **REGISTRY-2594:** Project statuses error on PUT (#699) ([fa81e30](https://github.com/HDRUK/safepeopleregistry-api/commit/fa81e303d1a2ddd7c7a40c501f8dae379798fcfd))
* **REGISTRY-2606:** Bypass verification needed where user email login… (#706) ([a800bdf](https://github.com/HDRUK/safepeopleregistry-api/commit/a800bdff3a28ce042a9d9e9ffa90e7990d63765b))
* **REGISTRY-2648:** Verify blowing up when org invited (#693) ([4381dbc](https://github.com/HDRUK/safepeopleregistry-api/commit/4381dbc347dd2f2b0efce6c49f75cff7340bdf9d))
* **REGISTRY-2675:** only notify the delegate not the world and no wrapper (#705) ([311c247](https://github.com/HDRUK/safepeopleregistry-api/commit/311c247df4cd8cb6f999a8aed985b789e5b34e6f))

## [1.28.0](https://github.com/HDRUK/safepeopleregistry-api/compare/v1.27.0...v1.28.0) (2026-02-27)

### ✨ Features

* **GAT-8387:** added release workflow pipeline (#662) ([956d2d5](https://github.com/HDRUK/safepeopleregistry-api/commit/956d2d5a8e0bbadae26513646326dbb970c76e0b)), closes [GAT-8387](GAT-8387)
* **GAT-8387:** updated action checkout version (#663) ([30455f8](https://github.com/HDRUK/safepeopleregistry-api/commit/30455f8f22b8f6c08bb5752c80dbf82f8b62ae42)), closes [GAT-8387](GAT-8387)
* **REGISTRY-0000:** sro resetting org system_approved (#657) ([b922019](https://github.com/HDRUK/safepeopleregistry-api/commit/b9220195a712a5ec6c0e7f66d0bde75d28f71d3d))
* **REGISTRY-1924:** add additional org fields (#668) ([219cd15](https://github.com/HDRUK/safepeopleregistry-api/commit/219cd154b617cce12911a0541adb9c1234fbf7f9))
* **REGISTRY-1954:** Add org invited status to workflow (#649) ([74c3347](https://github.com/HDRUK/safepeopleregistry-api/commit/74c3347b8b943adb69ac477e4398e7783619780b))
* **REGISTRY-2270:** reset status when affiliation email verified (#653) ([2536621](https://github.com/HDRUK/safepeopleregistry-api/commit/253662165616a9cff00355b211623af847349b8e))
* **REGISTRY-2332:** Prefill out email address in registration (#656) ([3e1b8de](https://github.com/HDRUK/safepeopleregistry-api/commit/3e1b8def6d3701bfd4a7a50eff88076297318995))
* **REGISTRY-2406:** add invited user invo to org (#655) ([dfadafe](https://github.com/HDRUK/safepeopleregistry-api/commit/dfadafe7e06b187cddb4b7c18b7e4c4b40dd531c))
* **REGISTRY-2425:** fix transitions for org state (#652) ([67da1a3](https://github.com/HDRUK/safepeopleregistry-api/commit/67da1a3ee8be4037167bd34f5c8c4824a4cfcd6e))
* **REGISTRY-2465:** Bulk custodian users (#654) ([58c75c1](https://github.com/HDRUK/safepeopleregistry-api/commit/58c75c1245209fc6dc5162c0636ae6270df1aff8))
* **REGISTRY-2542:** add org modelstate (#650) ([ac564c4](https://github.com/HDRUK/safepeopleregistry-api/commit/ac564c49635c2a0c7db2a6a2e6a0d6e1a92a7efe))
* **REGISTRY-2556:** bulk invite project users (#664) ([b95ec92](https://github.com/HDRUK/safepeopleregistry-api/commit/b95ec923d91ba1d77ff8ccb5cfc6ea590c30c279))

### 🐛 Bug Fixes

* **REGISTRY-0000:** Hidden fields for org (#665) ([fd2f5a6](https://github.com/HDRUK/safepeopleregistry-api/commit/fd2f5a6f12aec96a0de5ad737e8afb0ba303aa01))
* **REGISTRY-0000:** Increase timeout (#670) ([3e57496](https://github.com/HDRUK/safepeopleregistry-api/commit/3e57496868da13b95f49ce4a3c07d387d7a27d21))
* **REGISTRY-0000:** Stop defaulting every user to sro (#673) ([c7ff85e](https://github.com/HDRUK/safepeopleregistry-api/commit/c7ff85eca87ae7bf2c27c35266053b741f096221))
* **REGISTRY-1712:** Sponsor email not sent on newly invited org (#651) ([56d7100](https://github.com/HDRUK/safepeopleregistry-api/commit/56d71000912b91cdf8a38c92025c17bbcbcf0176))
* **REGISTRY-2332:** status for user affiliation (#660) ([2b1506d](https://github.com/HDRUK/safepeopleregistry-api/commit/2b1506debda2b047673cb60e55f709b205c1ffcb))
* **REGISTRY-2450:** Return projectOrganisation.project for header (#661) ([7a73279](https://github.com/HDRUK/safepeopleregistry-api/commit/7a73279e12147a0ac208570703387b915bfcf796))
* **REGISTRY-2563:** Statuses, Statuses, Statuses (#669) ([7c55f30](https://github.com/HDRUK/safepeopleregistry-api/commit/7c55f30770652a469018afe4405342daee86e994))

## [1.27.0](https://github.com/HDRUK/safepeopleregistry-api/compare/v1.26.3...v1.27.0) (2026-02-19)

### ✨ Features

* **GAT-0000:** Version and Status endpoint (#659) ([7b3fe7b](https://github.com/HDRUK/safepeopleregistry-api/commit/7b3fe7be686e3526a55c01482c724c0e23b6ad58)), closes [GAT-0000](GAT-0000)

## [1.26.3](https://github.com/HDRUK/safepeopleregistry-api/compare/v1.26.2...v1.26.3) (2026-02-10)

### 🐛 Bug Fixes

* **REGISTRY-0000:** update verification email (#647) ([199ae60](https://github.com/HDRUK/safepeopleregistry-api/commit/199ae60c97dd616be5d2c1b59458a245e87d2de3))

## [1.26.2](https://github.com/HDRUK/safepeopleregistry-api/compare/v1.26.1...v1.26.2) (2026-02-06)

### 🐛 Bug Fixes

* **REGISTRY-0000:** Force version (#645) ([1b42111](https://github.com/HDRUK/safepeopleregistry-api/commit/1b421111fdf1b84f6417a52f204ade338bd0bb6c))

## [1.26.1](https://github.com/HDRUK/safepeopleregistry-api/compare/v1.26.0...v1.26.1) (2026-02-06)

### 🐛 Bug Fixes

* **REGISTRY-0000:** Use tag for MJML package (#643) ([953c792](https://github.com/HDRUK/safepeopleregistry-api/commit/953c792278e7ea148c0cfbd367247759f6efed91))

## [1.26.0](https://github.com/HDRUK/safepeopleregistry-api/compare/v1.25.1...v1.26.0) (2026-02-05)

### ✨ Features

* **REGISTRY-0000:** order by email logs (#641) ([03358e7](https://github.com/HDRUK/safepeopleregistry-api/commit/03358e7dff9b9dba9164ab11c625aa3e1cb47c6f))

### 🐛 Bug Fixes

* **REGISTRY-0000:** extend try and catch email job (#640) ([34ba46f](https://github.com/HDRUK/safepeopleregistry-api/commit/34ba46f478de5b1ef8e32ec3722a46134a3e2346))

## [1.25.1](https://github.com/HDRUK/safepeopleregistry-api/compare/v1.25.0...v1.25.1) (2026-02-05)

### 🐛 Bug Fixes

* **REGISTRY-0000:** revert update cache (#638) ([83025ff](https://github.com/HDRUK/safepeopleregistry-api/commit/83025ff42f6b9ff55e50263156176f13b540c99b))

## [1.25.0](https://github.com/HDRUK/safepeopleregistry-api/compare/v1.24.0...v1.25.0) (2026-02-05)

### ✨ Features

* **REGISTRY-2409:** Pending invites - search by invite date and email (#611) ([4e24f9f](https://github.com/HDRUK/safepeopleregistry-api/commit/4e24f9facf24dd5c01924010ec5196fb7ed55e58))
* **REGISTRY-2417:** Training expiration notification & email (#604) ([ce84fe4](https://github.com/HDRUK/safepeopleregistry-api/commit/ce84fe45a1f48a03f83b30a93a6b3af52ff0c777))
* **REGISTRY-2419:** Security expiration notification & email (#607) ([bbdb17d](https://github.com/HDRUK/safepeopleregistry-api/commit/bbdb17df3841e1deedad8060de65278d56fc59dc))
* **REGISTRY-2420:** History for Organisation (#615) ([ef0acb7](https://github.com/HDRUK/safepeopleregistry-api/commit/ef0acb71d4df80cedd5e470bbc0f4c954b1c5d7e))
* **REGISTRY-2439:** checking email (#614) ([f5cc82c](https://github.com/HDRUK/safepeopleregistry-api/commit/f5cc82c5124df465a480169fb404a3e878020196))
* **REGISTRY-2476:** addtions to search (#633) ([c5f1640](https://github.com/HDRUK/safepeopleregistry-api/commit/c5f16401dc31c1728a40e30b7efcdf03a59d44f3))
* **REGISTRY-2481:** Create table for sent email history (#624) ([4ae165d](https://github.com/HDRUK/safepeopleregistry-api/commit/4ae165d186512ed4246dd5ec1d49e9185ea9ecef))
* **REGISTRY-2483:** get endpoint for email log (#629) ([4362d0f](https://github.com/HDRUK/safepeopleregistry-api/commit/4362d0f2837a23b77e8a2a3cd4a40dc370c2a03d))
* **REGISTRY-2484:** Integrate with sendgrid for getting data out using a message-id (#626) ([3a17b9b](https://github.com/HDRUK/safepeopleregistry-api/commit/3a17b9bd5c15b91d725cf0d6973ec827f33eef6e))
* **REGISTRY-2484:** Integrate with sendgrid for getting data out using a message-id (#630) ([589d43e](https://github.com/HDRUK/safepeopleregistry-api/commit/589d43ea96b05b39f4d175703b611d4ab226062e))
* **REGISTRY-2485:** remove throw error (#636) ([bb714c3](https://github.com/HDRUK/safepeopleregistry-api/commit/bb714c31a9d691a4f6882f24f86cf9773c66fd65))
* **REGISTRY-2486:** sendgid webhook (#635) ([2dc72fa](https://github.com/HDRUK/safepeopleregistry-api/commit/2dc72fa2f45b49f62f20bd26b43b85c98c9af1a9))
* **REGISTRY-2487:** retrigger a failed email via an endpoint (#627) ([55aeadf](https://github.com/HDRUK/safepeopleregistry-api/commit/55aeadf6d8b9f7892b7bcbca0f345c06928095ad))
* **REGISTRY-2500:** failed jobs slack notifcation (#631) ([d710c4f](https://github.com/HDRUK/safepeopleregistry-api/commit/d710c4f776f97efc4aef4d09216f437616b45843))

### 🐛 Bug Fixes

* **REGISTRY-0000:** Custodian ID for custodian user (#608) ([29cacc9](https://github.com/HDRUK/safepeopleregistry-api/commit/29cacc9c13a5b2efd24f21846f08b3997b79b0f6))
* **REGISTRY-0000:** update config (#634) ([6b1fdd8](https://github.com/HDRUK/safepeopleregistry-api/commit/6b1fdd873e6aac6198107523ee1fc26337244775))
* **REGISTRY-0000:** Update dockerfile (#632) ([cf8fad3](https://github.com/HDRUK/safepeopleregistry-api/commit/cf8fad37dc94fb60b453f0f06ba6a7f7cd1bba5c))
* **REGISTRY-2436:** Stopped  ORCiD scanner thing - temp (#609) ([42909d3](https://github.com/HDRUK/safepeopleregistry-api/commit/42909d3c09ac7bf8e914eb15b36654c427e609f1))
* **REGISTRY-2440:** Change email > failure to send email (#610) ([0030cbd](https://github.com/HDRUK/safepeopleregistry-api/commit/0030cbd5c3e5b4c479b335be1e8c81ccc23cfcd4))
* **REGISTRY-2467:** re-implements the validate endpoint for custodian… (#616) ([ed7f342](https://github.com/HDRUK/safepeopleregistry-api/commit/ed7f3421b2ef676a3f98268dd82999ff994e5dca))

## [1.24.0](https://github.com/HDRUK/safepeopleregistry-api/compare/v1.23.0...v1.24.0) (2026-01-12)

### ✨ Features

* **REGISTRY-1716:** email change (#587) ([f6e1e3b](https://github.com/HDRUK/safepeopleregistry-api/commit/f6e1e3b3350910764b745c93a31bad2a5d09622a))
* **REGISTRY-1717:** orcid user affiliation (#599) ([02ed97d](https://github.com/HDRUK/safepeopleregistry-api/commit/02ed97d9d1e9b6470ef6c93994c57a4ebd616800))
* **REGISTRY-2244:** modify org history endpoint (#603) ([72fb43f](https://github.com/HDRUK/safepeopleregistry-api/commit/72fb43f576256b16040d5f6820f12233c8bc9acf))
* **REGISTRY-2295:** user and organisation notifications automated flags (#598) ([9679d41](https://github.com/HDRUK/safepeopleregistry-api/commit/9679d4181eec2379dc86d07f64a06c993b9b84e4))
* **REGISTRY-2431:** model state for sponsor (#601) ([5c03eb6](https://github.com/HDRUK/safepeopleregistry-api/commit/5c03eb61a0ace5c2a4b984dbd14b7ffe79d9db31))
* **REGISTRY-2433:** sponsorship add project id in notifications (#602) ([c0664b0](https://github.com/HDRUK/safepeopleregistry-api/commit/c0664b014598079930f4d16b36ad0f9c22cbce2a))

### 🐛 Bug Fixes

* **REGISTRY-0000:** fix emails and register users (#590) ([5b38d6b](https://github.com/HDRUK/safepeopleregistry-api/commit/5b38d6b5d1bea4a4f68d4a97eee1686282bb8d69))
* **REGISTRY-0000:** send email sponsorship request (#588) ([ebdf355](https://github.com/HDRUK/safepeopleregistry-api/commit/ebdf35558e9a66096e8f96c33cbde4972a9e29a6))
* **REGISTRY-1853:** update notifications send to organisations (#595) ([1430a3b](https://github.com/HDRUK/safepeopleregistry-api/commit/1430a3b35917edbba1ef694894989f7a88595f6a))
* **REGISTRY-1988:** pagination to org history (#597) ([df2653f](https://github.com/HDRUK/safepeopleregistry-api/commit/df2653f8de38c6557ad1fe1103126c9a97f6a4ab))
* **REGISTRY-2258:** auth required for get all features (#592) ([1afbe0c](https://github.com/HDRUK/safepeopleregistry-api/commit/1afbe0c92135b2dd13000a49c3844f8ca6292343))
* **REGISTRY-2314:** Update affiliation flow (#589) ([0a99743](https://github.com/HDRUK/safepeopleregistry-api/commit/0a99743299076d6780e365091b0bb72f4aacc6de))
* **REGISTRY-2314:** Update affiliation flow: User invites Organisation Part 3 (#591) ([4767482](https://github.com/HDRUK/safepeopleregistry-api/commit/476748223e1c6180ee30e2c9d075766d26858f5c))
* **REGISTRY-2346:** Update affiliation flow (#593) ([7a5aadb](https://github.com/HDRUK/safepeopleregistry-api/commit/7a5aadb46326a2841976593239c7d3fa60823f36))
* **REGISTRY-2346:** Update affiliation flow: Custodian invites User (#586) ([a0d53d8](https://github.com/HDRUK/safepeopleregistry-api/commit/a0d53d8ca61d9f7d5162ac12d48e50c38d00bc69))
* **REGISTRY-2346:** update states custodian invite user and organisation (#594) ([f67fb22](https://github.com/HDRUK/safepeopleregistry-api/commit/f67fb225218917bc06bafe5c902991178cf1646b))
* **REGISTRY-2410:** Custodian - cannot find new User in add User list (#596) ([1f142ac](https://github.com/HDRUK/safepeopleregistry-api/commit/1f142ac7912ac219e8dc5b245c81da640a595b09))

## [1.23.0](https://github.com/HDRUK/safepeopleregistry-api/compare/v1.22.0...v1.23.0) (2025-12-17)

### ✨ Features

* **REGISTRY-0000:** Sponsorship approved or rejected (#573) ([32bd354](https://github.com/HDRUK/safepeopleregistry-api/commit/32bd35496f7a9166638cfc28236d7183e66c07db))
* **REGISTRY-1709:** Add sponsorship to project (#571) ([f8a0eb6](https://github.com/HDRUK/safepeopleregistry-api/commit/f8a0eb63009da3522451486e1e1560ec464047b3))
* **REGISTRY-1710:** tweaks for sponsorship (#580) ([5b4909d](https://github.com/HDRUK/safepeopleregistry-api/commit/5b4909decbc26927cdc78e6312a9ccc386a65748))
* **REGISTRY-1713:** List projects with sponsorships (#572) ([c11e409](https://github.com/HDRUK/safepeopleregistry-api/commit/c11e4097852ed7f6e6ff972afe25a50abdc88479))
* **REGISTRY-2289:** BIG TASK: Notifications: minimum content for Custodian actions (#564) ([d10fc73](https://github.com/HDRUK/safepeopleregistry-api/commit/d10fc730920dcd2d2727027b4d1a53d39725ea80))
* **REGISTRY-2333:** Update swagger title (#565) ([799b003](https://github.com/HDRUK/safepeopleregistry-api/commit/799b00307890e5f0ec1f23d8668e03c1f46af75c))
* **REGISTRY-2345:** Update affiliation flow: Organisation invites User (#578) ([c49a085](https://github.com/HDRUK/safepeopleregistry-api/commit/c49a0855b9f30a88d7414a8da70795037d59eb41))
* **REGISTRY-2356:** Christmas Seeder (#575) ([2289a13](https://github.com/HDRUK/safepeopleregistry-api/commit/2289a13615648c0be948dd2533c4ee7add062176))
* **REGISTRY-2356:** Christmas Seeder (#577) ([152764e](https://github.com/HDRUK/safepeopleregistry-api/commit/152764e9261c674378f152bd2b7beb7ae4d485da))
* **REGISTRY-2365:** Resend Sponsorship request (#576) ([9acbf5c](https://github.com/HDRUK/safepeopleregistry-api/commit/9acbf5c4ff3cbd9d5318e170c711d0bf3fa6ce81))
* **REGISTRY-2366:** resend an invite based on organisation id (#579) ([d44dbb9](https://github.com/HDRUK/safepeopleregistry-api/commit/d44dbb9ec3c5021a702c2e67642ac4eb044c31de))
* **REGISTRY-2367:** new affiliation state (#581) ([29a5315](https://github.com/HDRUK/safepeopleregistry-api/commit/29a5315af5836c22e1adbaeefbf0ca5d7c779cea))

### 🐛 Bug Fixes

* **REGISTRY-0000:** change to text (#569) ([dc4cb0e](https://github.com/HDRUK/safepeopleregistry-api/commit/dc4cb0e92efbbb828f74f754afa0203acccf3897))
* **REGISTRY-0000:** fix pending invites column name (#583) ([71bd0f3](https://github.com/HDRUK/safepeopleregistry-api/commit/71bd0f33b9a991508fc1208094c8823d54b04542))
* **REGISTRY-0000:** Migrate features to text (#567) ([a32ff5b](https://github.com/HDRUK/safepeopleregistry-api/commit/a32ff5bde3dd1269bccdcf6e58c7d2e313f34034))
* **REGISTRY-0000:** update url to notifications (#568) ([0d40645](https://github.com/HDRUK/safepeopleregistry-api/commit/0d406453ebdaba989fa505001f279496ad4b348d))
* **REGISTRY-0000:** user registry (#563) ([17f89ec](https://github.com/HDRUK/safepeopleregistry-api/commit/17f89ecde8234729a97e687cb74ab15dc610de94))
* **REGISTRY-2258:** Fix for pennant and feature controller (#566) ([f316b43](https://github.com/HDRUK/safepeopleregistry-api/commit/f316b43e566f999ab156501fda43688cf120e2b1))
* **REGISTRY-2275:** Custodian/Project/User/Automated flags | Affiliation flag (#570) ([08963e1](https://github.com/HDRUK/safepeopleregistry-api/commit/08963e10250a4fab262aca2570df193ca016259d))
* **REGISTRY-2316:** Error 500 when we call history endpoint user history (#560) ([bad054a](https://github.com/HDRUK/safepeopleregistry-api/commit/bad054ae0d1f664e1d6dd8708522df4d74aaa08c))
* **REGISTRY-2316:** update per page in history (#561) ([b476cd4](https://github.com/HDRUK/safepeopleregistry-api/commit/b476cd42b8cd94f5967e0fcb4eba7fc70ff34e83))
* **REGISTRY-2346:** add users in project (#582) ([05a50df](https://github.com/HDRUK/safepeopleregistry-api/commit/05a50df2e970e6f4db2f65d6de53b9655c2f6be9))
* **REGISTRY-2346:** fix organisation status (#584) ([6bedd22](https://github.com/HDRUK/safepeopleregistry-api/commit/6bedd222a8ec55b0a04420b7c0bd4bc10607ea78))
* **REGISTRY-2364:** Automated flags: Sanctioned country failed (#574) ([af8ff30](https://github.com/HDRUK/safepeopleregistry-api/commit/af8ff308db62784218c98915d28bb1bc2026cf69))

## [1.22.0](https://github.com/HDRUK/safepeopleregistry-api/compare/v1.21.0...v1.22.0) (2025-12-04)

### ✨ Features

* **REGISTRY-1234:** remove ; from example ([a26f101](https://github.com/HDRUK/safepeopleregistry-api/commit/a26f1013687c17a2ee7c87ce92fcbc4ad32e99b5))
* **REGISTRY-1234:** remove ; from example (#550) ([aca2806](https://github.com/HDRUK/safepeopleregistry-api/commit/aca2806d604a09efacec39caee1a5c42be9bbda9))
* **REGISTRY-1853:** Notifications: minimum content (#549) ([e43b81a](https://github.com/HDRUK/safepeopleregistry-api/commit/e43b81a57ab455a75d7271f729363173c07dc0a4))
* **REGISTRY-2207:** Super-admin: Add ability to filter invites by user group and new data (#556) ([c44fd36](https://github.com/HDRUK/safepeopleregistry-api/commit/c44fd368f6f7e9d9e26de37ffa6414c0ffd20f0f))
* **REGISTRY-2237:** filter no affilliations (#551) ([2b18d12](https://github.com/HDRUK/safepeopleregistry-api/commit/2b18d12cdee8645bfd7e92c40ef5c3bedfc56f0f))
* **REGISTRY-2261:** Affiliation status - Custodian invites User and Org (#548) ([4c28e73](https://github.com/HDRUK/safepeopleregistry-api/commit/4c28e73b2928847a3ba9c22c7e246ad3e7be6033))
* **REGISTRY-2289:** Notifications: minimum content for Organisation actions (#552) ([ca649eb](https://github.com/HDRUK/safepeopleregistry-api/commit/ca649eb3c8e53db2f24ea88e77a034c35bf68625))

### 🐛 Bug Fixes

* **REGISTRY-2300:** Custodians show that manual checks look like they are overwriting for… (#553) ([44d84c5](https://github.com/HDRUK/safepeopleregistry-api/commit/44d84c5e82c448f9a9059aa2121d96025327558b))
* **REGISTRY-2306:** Custodian comments on Validation status (#554) ([e34503c](https://github.com/HDRUK/safepeopleregistry-api/commit/e34503c7f76213073d7de71be2fbaec6c1a0d54a))
* **REGISTRY-2306:** Custodian comments on Validation status changes should ONLY be viewab… (#557) ([abcc015](https://github.com/HDRUK/safepeopleregistry-api/commit/abcc015b264fbf3850bb906acb7e7d39933dd3df))
* **REGISTRY-2308:** Email verification link not working (#555) ([67ef7c7](https://github.com/HDRUK/safepeopleregistry-api/commit/67ef7c761d60e771348141f47ad205b0bbad615d))

## [1.21.0](https://github.com/HDRUK/safepeopleregistry-api/compare/v1.20.0...v1.21.0) (2025-11-26)

### ✨ Features

* **REGISTRY-1851:** Affiliation status - Org invites User (#539) ([4298331](https://github.com/HDRUK/safepeopleregistry-api/commit/4298331fd79fd77f27d3acfcaaf452cdefb4addb))
* **REGISTRY-2260:** Affiliation status - User invites Org (#546) ([f556748](https://github.com/HDRUK/safepeopleregistry-api/commit/f556748075315ca98d6f671c30bed442522fb1bd))

### 🐛 Bug Fixes

* **REGISTRY-0000:** change column type in table (#544) ([5792e60](https://github.com/HDRUK/safepeopleregistry-api/commit/5792e602a81c881558dbbd27f2c6a1fb5b138fc1))
* **REGISTRY-2284:** change claim user endpoint ([49351ce](https://github.com/HDRUK/safepeopleregistry-api/commit/49351ce393e4d364b511990625719a55b05710cc))
* **REGISTRY-2284:** change claim user endpoint (#545) ([c3da40e](https://github.com/HDRUK/safepeopleregistry-api/commit/c3da40ee1c958d6e5e3d12af40c9bf2cb98e386b))

## [1.20.0](https://github.com/HDRUK/safepeopleregistry-api/compare/v1.19.1...v1.20.0) (2025-11-25)

### ✨ Features

* **REGISTRY-1422:** update the email in keycloak when is updated in profile (#527) ([3a9e8ec](https://github.com/HDRUK/safepeopleregistry-api/commit/3a9e8ec381e1a2f77f3881e8f3aae3942a84a24f))
* **REGISTRY-1705:** Custodian | Add 1 status (#533) ([ad4593e](https://github.com/HDRUK/safepeopleregistry-api/commit/ad4593e2f381de700689d70f8205f4abb809627a))
* **REGISTRY-1875:** Organisation (SRO application) super-admin interface - notifications (#538) ([2f894c4](https://github.com/HDRUK/safepeopleregistry-api/commit/2f894c4b14ce3baaa3e00364e594743091922e4f))
* **REGISTRY-2028:** add count of affiliation (#526) ([756c898](https://github.com/HDRUK/safepeopleregistry-api/commit/756c898ef173d2a8505adc98537724fc59e90e90))
* **REGISTRY-2190:** Give Registry team permissions to view Horizon-prod email statuses (#517) ([8e15c52](https://github.com/HDRUK/safepeopleregistry-api/commit/8e15c528361cc65b7e022e0eb2e252faeb5fabd5))
* **REGISTRY-2208:** test users (#515) ([99431ea](https://github.com/HDRUK/safepeopleregistry-api/commit/99431ea130cd2e85cbd9804e27bd388887b6a647))
* **REGISTRY-2217:** Make super-admin custodian invites intelligent (#521) ([451907d](https://github.com/HDRUK/safepeopleregistry-api/commit/451907dd3f312bb999ab2c2fd70a219e25d514b4))
* **REGISTRY-2224:** Admins > resend invites (#519) ([f9a279e](https://github.com/HDRUK/safepeopleregistry-api/commit/f9a279e6b26195feb4c7a873e55a2278d7232c46))
* **REGISTRY-2226:** Keycloak - enable resend of email verification (#530) ([7b96c92](https://github.com/HDRUK/safepeopleregistry-api/commit/7b96c92d42980a7d773b49bb11f1f308767e11f6))
* **REGISTRY-2237:** switch to post (#524) ([9be2170](https://github.com/HDRUK/safepeopleregistry-api/commit/9be2170e05fef09b8567690f726db73185201728))
* **REGISTRY-2256:** add users needs filter users (#536) ([d6a0b70](https://github.com/HDRUK/safepeopleregistry-api/commit/d6a0b70335cfe364ffeb0986e7d5502cd5d5b2bc))
* **REGISTRY-2276:** Update Organisation invite email template (from both Custodian or User) (#540) ([6d22b1c](https://github.com/HDRUK/safepeopleregistry-api/commit/6d22b1cda0d7e7e38a38867b24eaaecd319ad875))

### 🐛 Bug Fixes

* **REGISTRY-0000:** update 18nov (#532) ([9015a8f](https://github.com/HDRUK/safepeopleregistry-api/commit/9015a8f1fb8eaee4d0b8390f76b8ed02f2ed32b2))
* **REGISTRY-0000:** update docker file for frankenphp (#542) ([a257911](https://github.com/HDRUK/safepeopleregistry-api/commit/a257911b8c51ff37105c9e1a5e5f1b574a557a29))
* **REGISTRY-0000:** update organisation invite by custodian or user (#541) ([fa535e7](https://github.com/HDRUK/safepeopleregistry-api/commit/fa535e7868361ca259106d9586c307f7313a7d2d))
* **REGISTRY-1834:** Custodian | Add 2 User validation statuses (#537) ([de48d5d](https://github.com/HDRUK/safepeopleregistry-api/commit/de48d5d24a621888e79fe75a42b64e2efdb73d84))
* **REGISTRY-1929:** Registration - all users are getting registry entries (#531) ([d56e99a](https://github.com/HDRUK/safepeopleregistry-api/commit/d56e99a04cc34c207bef3030de0d9580f2ab5815))
* **REGISTRY-2044:** missing org unique id (#525) ([5e6c7ed](https://github.com/HDRUK/safepeopleregistry-api/commit/5e6c7edd9752c3b1f8a09454bdab415fef5ec32a))
* **REGISTRY-2210:** missing var (#518) ([1fc78d8](https://github.com/HDRUK/safepeopleregistry-api/commit/1fc78d8e8acaf81ac97140e1c22441dd9ef1a197))
* **REGISTRY-2214:** Custodian/Project/Safe People/Add new User | Fail when on page 1 of rota when inviting new (#520) ([12e39c7](https://github.com/HDRUK/safepeopleregistry-api/commit/12e39c7470ea8b95c887bf8a7202860dfb15e4bb))
* **REGISTRY-2225:** reset keycloak password (#528) ([2d9d7ab](https://github.com/HDRUK/safepeopleregistry-api/commit/2d9d7aba872ca781cd23fc98f14c6c90eb97b988))
* **REGISTRY-2238:** predis to phpredis (#523) ([52b9c19](https://github.com/HDRUK/safepeopleregistry-api/commit/52b9c19505dd81031e931a831dee1aa3f21a731e))
* **REGISTRY-2259:** Resend invites from admin generate 404 errors (#534) ([00df534](https://github.com/HDRUK/safepeopleregistry-api/commit/00df5347fc2b9ad3b23eae0723c4e7af9cae86d3))

## [1.19.1](https://github.com/HDRUK/safepeopleregistry-api/compare/v1.19.0...v1.19.1) (2025-11-05)

### 🐛 Bug Fixes

* **REGISTRY-2203:** fix button (#514) ([ab3b6a9](https://github.com/HDRUK/safepeopleregistry-api/commit/ab3b6a967104982c93b775252f139a1358bd5be5))

## [1.19.0](https://github.com/HDRUK/safepeopleregistry-api/compare/v1.18.0...v1.19.0) (2025-11-04)

### ✨ Features

* **REGISTRY-0000:** update hdruk laravel-mjml package version ([8e9be35](https://github.com/HDRUK/safepeopleregistry-api/commit/8e9be350ea2beb16e5627aa69e289bdef41ba1ca))
* **REGISTRY-0000:** update hdruk laravel-mjml package version (#511) ([a0a79d9](https://github.com/HDRUK/safepeopleregistry-api/commit/a0a79d9468f08ae678a6d82d51156911f8541a88))
* **REGISTRY-1127:** Organisations - add parent organisation (subsidiaries) (#506) ([14182fd](https://github.com/HDRUK/safepeopleregistry-api/commit/14182fda2acb45031e2ace932118640007690e2d))

### 🐛 Bug Fixes

* **REGISTRY-2047:** Write & test a process for using feature flags (#502) ([7be4afe](https://github.com/HDRUK/safepeopleregistry-api/commit/7be4afef4c7de29eed5b36d1894ccda8ddff9eb9))
* **REGISTRY-2188:** Custodian users should NOT have registry_ids (#508) ([5b0aa0f](https://github.com/HDRUK/safepeopleregistry-api/commit/5b0aa0fb6675e655a7b6ef9a49ccacebb9352f4a))
* **REGISTRY-2189:** update with tries for jobs (#507) ([77e16a9](https://github.com/HDRUK/safepeopleregistry-api/commit/77e16a9ad1f8ff76d9a16d4136c1261b5c6248cb))
* **REGISTRY-2194:** sending email in gmail it is not visible (#510) ([7c34102](https://github.com/HDRUK/safepeopleregistry-api/commit/7c3410273bfcaf4c43904b1906e1dd70f73173e9))

## [1.18.0](https://github.com/HDRUK/safepeopleregistry-api/compare/v1.17.1...v1.18.0) (2025-11-04)

### ✨ Features

* **REGISTRY-0000:** update hdruk laravel-mjml package version (#511) ([6099b4c](https://github.com/HDRUK/safepeopleregistry-api/commit/6099b4c075c15b115e51a092bf93aa91c03befba))
* **REGISTRY-0000:** update hdruk laravel-mjml package version (#511) (#512) ([3ce56e8](https://github.com/HDRUK/safepeopleregistry-api/commit/3ce56e87a45acc1be3787002edf7e986761c9fae))

## [1.17.1](https://github.com/HDRUK/safepeopleregistry-api/compare/v1.17.0...v1.17.1) (2025-10-24)

### 🐛 Bug Fixes

* **REGISTRY-0000:** migration project details field (#503) ([f6d8306](https://github.com/HDRUK/safepeopleregistry-api/commit/f6d8306d00658da2a13a293a8831ce78e6ca7ea3))

## [1.17.0](https://github.com/HDRUK/safepeopleregistry-api/compare/v1.16.0...v1.17.0) (2025-10-24)

### ✨ Features

* **REGISTRY-1930:** Custodians - invite user (#497) ([7ef2159](https://github.com/HDRUK/safepeopleregistry-api/commit/7ef215993161a12cb02f57f0d8c8ebb2aca1a51c))

### 🐛 Bug Fixes

* **REGISTRY-1428:** Consistently setup Gates/Policies for endpoint permissions (#487) ([b835de3](https://github.com/HDRUK/safepeopleregistry-api/commit/b835de33175e99591bb1320a13abb7e2d2de859b))
* **REGISTRY-1429:** Eloquent Review (#490) ([3f9400a](https://github.com/HDRUK/safepeopleregistry-api/commit/3f9400a0d5ed6580b7a0a3f963e6abf6a263b530))
* **REGISTRY-1701:** Organisations | SRO Profile fix seeding (#493) ([cbc04e9](https://github.com/HDRUK/safepeopleregistry-api/commit/cbc04e99c7fc50d48293b59fe77bae6bc2c11f8b))
* **REGISTRY-1908:** Fix seeding of projects to have data for all 5 Safes (#494) ([5acc7b1](https://github.com/HDRUK/safepeopleregistry-api/commit/5acc7b1f84c86c78803945ea9870fd5a9e064e37))
* **REGISTRY-1982:** Replace MJML api with better email set-up (#499) ([0d22876](https://github.com/HDRUK/safepeopleregistry-api/commit/0d22876d4e2549f4bfef17411b825de16966b6fe))
* **REGISTRY-2049:** update checks authorisation endpoint (#495) ([a82f22e](https://github.com/HDRUK/safepeopleregistry-api/commit/a82f22e550a2da85fbf5b17c79c14d2c517e3c94))
* **REGISTRY-2128:** Organisation can't affiliate a user (#491) ([c33a887](https://github.com/HDRUK/safepeopleregistry-api/commit/c33a8879ea7f23016717d7e864727c8b05b15d95))
* **REGISTRY-2144:** Custodian/User/Projects | Validation status not updated for other projects (#492) ([a7ee9e5](https://github.com/HDRUK/safepeopleregistry-api/commit/a7ee9e5b0279f53f11c07ae5c66686f5f5dde513))
* **REGISTRY-2171:** Email - The button 'Confirm Organisation' doesn't link to anything (#496) ([7c34dc8](https://github.com/HDRUK/safepeopleregistry-api/commit/7c34dc875aacd1fbbb519b728c097a8a8d42fa70))
* **REGISTRY-2176:** Enable User addition with an unconfirmed Organisation by a Custodian (#498) ([13b1ccd](https://github.com/HDRUK/safepeopleregistry-api/commit/13b1ccda9a6a1c06dc5677ee923af08f5b039187))
* **REGISTRY-2177:** projects erroring on Safe Outputs (#500) ([1a33ab2](https://github.com/HDRUK/safepeopleregistry-api/commit/1a33ab2ef78a459dc7ea3c999d1ea194c119df71))

## [1.16.0](https://github.com/HDRUK/safepeopleregistry-api/compare/v1.15.0...v1.16.0) (2025-10-10)

### ✨ Features

* **REGISTRY-1873:** Organisation | SRO application - notifications & emails (#481) ([75dde57](https://github.com/HDRUK/safepeopleregistry-api/commit/75dde57170185b242ee1a2a3f2620acb033acb25))

### 🐛 Bug Fixes

* **REGISTRY-0000:** fix for horizon (#483) ([53b0896](https://github.com/HDRUK/safepeopleregistry-api/commit/53b0896b44a30ec9f1c9352adf46f94dfd661a1c))
* **REGISTRY-0000:** get projects by id and userid (#482) ([e21909f](https://github.com/HDRUK/safepeopleregistry-api/commit/e21909f052f9484ef479cc8b0efc9f0c86034f6a))
* **REGISTRY-0000:** update email and others (#485) ([b51fd9d](https://github.com/HDRUK/safepeopleregistry-api/commit/b51fd9d2f37b0ff090de55685b6a7c67f7760e5d))
* **REGISTRY-2038:** Adding multiple users to projects, the response is not complete (#484) ([cb965bf](https://github.com/HDRUK/safepeopleregistry-api/commit/cb965bf802504cc6b73f246023849eb47e80a725))

## [1.15.0](https://github.com/HDRUK/safepeopleregistry-api/compare/v1.14.2...v1.15.0) (2025-10-09)

### ✨ Features

* **REGISTRY-0000:** Implement microsoft graph api for additional ema… (#477) ([69f49ed](https://github.com/HDRUK/safepeopleregistry-api/commit/69f49edd5135b0da3faebd2c8d7885d4241f9554))
* **REGISTRY-0000:** Implement microsoft graph api for additional email sending options ([4603326](https://github.com/HDRUK/safepeopleregistry-api/commit/4603326d7bb2e473bcffd26b0525739a424664c4))

### 🐛 Bug Fixes

* **REGISTRY-2024:** Organisations > Delegates - sign up link going to homepage (#479) ([feae401](https://github.com/HDRUK/safepeopleregistry-api/commit/feae4015b979f2884f1bf4332fa5c57d3f1f50b0))

## [1.14.2](https://github.com/HDRUK/safepeopleregistry-api/compare/v1.14.1...v1.14.2) (2025-10-09)

### 🐛 Bug Fixes

* **REGISTRY-2010:** Organisations > Projects - filters not working (#475) ([4cfaf55](https://github.com/HDRUK/safepeopleregistry-api/commit/4cfaf55dc5b8ea51fbc32da5525a04c9276f8698))

## [1.14.1](https://github.com/HDRUK/safepeopleregistry-api/compare/v1.14.0...v1.14.1) (2025-10-08)

### 🐛 Bug Fixes

* **REGISTRY-1927:** validations of parameters in requests - part three (#469) ([3265922](https://github.com/HDRUK/safepeopleregistry-api/commit/3265922164edb3bc0b48a244072bd56302e147d7))
* **REGISTRY-1927:** validations of parameters in requests - second part (#467) ([844edb7](https://github.com/HDRUK/safepeopleregistry-api/commit/844edb7a0d2787538ff34ab865f5c7d342066471))
* **REGISTRY-2006:** Custodians > Organisations - forbidden error validated status (#470) ([ed60f95](https://github.com/HDRUK/safepeopleregistry-api/commit/ed60f952fb17c50afa2142f5f150c744b9d88cc2))
* **REGISTRY-2009:** Custodians > Organisations - 400 on validation_logs (#471) ([a59897d](https://github.com/HDRUK/safepeopleregistry-api/commit/a59897d4cf46deffc7aeafe0cc5d8b9524e9a061))
* **REGISTRY-2015:** The custodian can change the status of an organisation (#472) ([0ff5b6e](https://github.com/HDRUK/safepeopleregistry-api/commit/0ff5b6eaf7b6295d15b8de87f5a0209a514e65d1))
* **REGISTRY-2016:** return only own user for user entities (#473) ([cdf1dcf](https://github.com/HDRUK/safepeopleregistry-api/commit/cdf1dcf464af485bf7a4c4fcfe034e7133b279f7))
* **REGISTRY-2019:** Users > Projects - can't filter by date dropdown (#474) ([779362d](https://github.com/HDRUK/safepeopleregistry-api/commit/779362d9817c41faa3a181a3a7b47eeb7c7155d3))

## [1.14.0](https://github.com/HDRUK/safepeopleregistry-api/compare/v1.13.0...v1.14.0) (2025-10-03)

### ✨ Features

* **REGISTRY-1920:** change notifications (#466) ([e2a58d6](https://github.com/HDRUK/safepeopleregistry-api/commit/e2a58d6d53b86a7178b7d815c3ea351cb06bffd1))

### 🐛 Bug Fixes

* **REGISTRY-1900:** History missing (#463) ([e49a64e](https://github.com/HDRUK/safepeopleregistry-api/commit/e49a64eaea9cce313c1c727ce89c839e89420541))
* **REGISTRY-1990:** Fix setState on user creation (#465) ([328fc2c](https://github.com/HDRUK/safepeopleregistry-api/commit/328fc2c8b55fdb2a74bf15aee3908613809b31a9))

## [1.13.0](https://github.com/HDRUK/safepeopleregistry-api/compare/v1.12.0...v1.13.0) (2025-10-01)

### ✨ Features

* **REGISTRY-1294:** inivite code for org and sro (#447) ([8d20f68](https://github.com/HDRUK/safepeopleregistry-api/commit/8d20f688ae107ae98672d7021265ba6665ef6ff8))
* **REGISTRY-1662:** add project user get single user endpoint (#455) ([f3e0ee6](https://github.com/HDRUK/safepeopleregistry-api/commit/f3e0ee63251aa89ea2d03e62e422252e35a2dfa6))
* **REGISTRY-1836:** Organisation: automatic flags and history (#448) ([ad86c6e](https://github.com/HDRUK/safepeopleregistry-api/commit/ad86c6ee2aca8d9128e73c7a407a363a2834c8dc))
* **REGISTRY-1938:** Affiliations - verified email (#445) ([6a303f6](https://github.com/HDRUK/safepeopleregistry-api/commit/6a303f680b940012b394b4e44565daa7efee2e41))
* **REGISTRY-1939:** use url part (#459) ([d1a6a2e](https://github.com/HDRUK/safepeopleregistry-api/commit/d1a6a2e18604593c1c10b653b287bbcf61f49ce5))

### 🐛 Bug Fixes

* **REGISTRY-1484:** Custodian | Project/User | Automated flags | Making sure Flags config matches what's shown (#446) ([b27c75c](https://github.com/HDRUK/safepeopleregistry-api/commit/b27c75c9abf24d341c291efb2f51e03142ba3aea))
* **REGISTRY-1670:** Custodian invites User | Account should show up as Invited (#457) ([ff267b1](https://github.com/HDRUK/safepeopleregistry-api/commit/ff267b1aedf9b11f9a2a39a9923aeac6759a6b4a))
* **REGISTRY-1927:** Possible SQL Injection (#449) ([dc97a43](https://github.com/HDRUK/safepeopleregistry-api/commit/dc97a43fb4f9d5840531c24d0661cde41e69665f))
* **REGISTRY-1927:** validations of parameters in requests - first part (#458) ([7b204a8](https://github.com/HDRUK/safepeopleregistry-api/commit/7b204a85f8c217a6d0d890041b3cbe7e86ad9b5a))
* **REGISTRY-1934:** ORCiD scanner issue (#452) ([fe560b4](https://github.com/HDRUK/safepeopleregistry-api/commit/fe560b4ea281cb95c21fc5ed756bd5b6b72a5575))
* **REGISTRY-1942:** broken prod seeder (#444) ([2e5b3d7](https://github.com/HDRUK/safepeopleregistry-api/commit/2e5b3d7e1cd6a5e6d0c0c3ebcd75ed6b8498b04e))
* **REGISTRY-1949:** email notification error in preprod (#451) ([dd8fdf3](https://github.com/HDRUK/safepeopleregistry-api/commit/dd8fdf31636bd4080dc3996708b971eaac63f729))
* **REGISTRY-1964:** Organisation invite email spawned twice (#456) ([18a4259](https://github.com/HDRUK/safepeopleregistry-api/commit/18a42591dc0638e9c981a51e8e6183df027eece9))
* **REGISTRY-1986:** no org for affiliation in seeded data (#461) ([29d053f](https://github.com/HDRUK/safepeopleregistry-api/commit/29d053f07bd64b4159142075a65a4b1e4a222f09))

## [1.12.0](https://github.com/HDRUK/safepeopleregistry-api/compare/v1.11.0...v1.12.0) (2025-09-18)

### ✨ Features

* **REGISTRY-1816:** Organisation | Hide staff/students IDs of other affiliations (#439) ([7115e4d](https://github.com/HDRUK/safepeopleregistry-api/commit/7115e4de5c5a66dc60ec91f752a59dd999f1756f))
* **REGISTRY-1926:** All project users endpoint needs to display affiliation email address (#440) ([dde09f7](https://github.com/HDRUK/safepeopleregistry-api/commit/dde09f7effd4ba3449bd89db670c85e8cfbc234a))

### 🐛 Bug Fixes

* **REGISTRY-1234:** fix param for email (#437) ([ad0383c](https://github.com/HDRUK/safepeopleregistry-api/commit/ad0383c9e10378a5dbf5ad3515d5c4c9680f665b))
* **REGISTRY-1852:** Prevents ORCID running scans on user updates as w… (#441) ([7625129](https://github.com/HDRUK/safepeopleregistry-api/commit/76251294accdfa45d2c2e372f8bd79e2d04e910f))
* **REGISTRY-1852:** Prevents ORCID running scans on user updates as we are spamming ORCID API ([058a291](https://github.com/HDRUK/safepeopleregistry-api/commit/058a291190ceb41815e10caeb87978250bd7b562))
* **REGISTRY-1898:** Custodian/Project-User/Automated flags | Affiliation flag not updatin… (#438) ([b42f757](https://github.com/HDRUK/safepeopleregistry-api/commit/b42f75773107e81a9d9bdc1500b5bfc7fe13219e))

## [1.11.0](https://github.com/HDRUK/safepeopleregistry-api/compare/v1.10.1...v1.11.0) (2025-09-17)

### ✨ Features

* **REGISTRY-1459:** get project by id and user id for approvals (#434) ([5d9194e](https://github.com/HDRUK/safepeopleregistry-api/commit/5d9194e07f7893d532a99ed5d1ebe2f051075178))
* **REGISTRY-1893:** Potential Cross Site WebSocket Hijacking - Insufficient WebSocket Origin Validation (#429) ([f28c1e9](https://github.com/HDRUK/safepeopleregistry-api/commit/f28c1e93f99a70c3d2d2a2e7db55a07720fe7918))
* **REGISTRY-1897:** Distinguish User invite email based on invited by Org or by Custodian (#432) ([d62deef](https://github.com/HDRUK/safepeopleregistry-api/commit/d62deef0f7ddc8c988745beab1079f0b7b301328))
* **REGISTRY-1904:** include sro (#428) ([5251a68](https://github.com/HDRUK/safepeopleregistry-api/commit/5251a682f1e3f5bc374f9e6db16ee5df070f1f76))

### 🐛 Bug Fixes

* **REGISTRY-1234:** fix send user email invite for users (#433) ([25165e7](https://github.com/HDRUK/safepeopleregistry-api/commit/25165e78343f9f3842c8fee81227e32091cf53cb))
* **REGISTRY-1234:** send approver and administrator invite emails (#435) ([4c5998e](https://github.com/HDRUK/safepeopleregistry-api/commit/4c5998ea1f9bd8317b07b8dd8dc6a44e53989251))
* **REGISTRY-1234:** update start OrcIDScanner job (#431) ([3522f35](https://github.com/HDRUK/safepeopleregistry-api/commit/3522f35bace5496861e43d9177007330a60dc0ac))
* **REGISTRY-1895:** filter projects (#427) ([da107ce](https://github.com/HDRUK/safepeopleregistry-api/commit/da107ce789d989d51b5c29cd812aab26374ff9e2))

## [1.10.1](https://github.com/HDRUK/safepeopleregistry-api/compare/v1.10.0...v1.10.1) (2025-09-12)

### 🐛 Bug Fixes

* **REGISTRY-1885:** Organisations > Approval - 403 for admin (#425) ([3a4ffa8](https://github.com/HDRUK/safepeopleregistry-api/commit/3a4ffa848aac3e3143cb5e5dd50b794a615d7163))

## [1.10.0](https://github.com/HDRUK/safepeopleregistry-api/compare/v1.9.0...v1.10.0) (2025-09-11)

### ✨ Features

* **REGISTRY-1234:** update notification new project template (#410) ([d7172ef](https://github.com/HDRUK/safepeopleregistry-api/commit/d7172ef5bdcacdd1b90168628d82934e35300e0c))
* **REGISTRY-1317:** Remove Misconduct / Criminal record from Validation check bank (not allowed to hold any information on this) (#407) ([42b725f](https://github.com/HDRUK/safepeopleregistry-api/commit/42b725f93150923e18e1236ed6283fe869a8eaaa))
* **REGISTRY-1617:** Update Researcher Invite Email Template (#398) ([2a756e3](https://github.com/HDRUK/safepeopleregistry-api/commit/2a756e3eeee2dcb31e8a5ba39398e715054b9143))
* **REGISTRY-1623:** Email: Affiliation request for Delegate (#400) ([4ddd321](https://github.com/HDRUK/safepeopleregistry-api/commit/4ddd321b52e3d7c84588a55719c8b58b34305886))
* **REGISTRY-1700:** Update email text (#408) ([6b81827](https://github.com/HDRUK/safepeopleregistry-api/commit/6b81827f7383cc405cf6a809efcd08d6b63f43ab))
* **REGISTRY-1707:** emails and notifications for add user to projects (#404) ([2a693df](https://github.com/HDRUK/safepeopleregistry-api/commit/2a693dfa617ccf077af981bf710ef58b68a3795f))
* **REGISTRY-1746:** validation status (#399) ([8f075d3](https://github.com/HDRUK/safepeopleregistry-api/commit/8f075d3584b8978d5f07f046d92132d0a4abaf95))
* **REGISTRY-1762:** Email templates for invitations (#405) ([0d8dcac](https://github.com/HDRUK/safepeopleregistry-api/commit/0d8dcac3a88ce06f4845679ad2f9e469b205b13d))
* **REGISTRY-1767:** Superadmin | Make email invite to Data Custodian work (#397) ([2d80ddd](https://github.com/HDRUK/safepeopleregistry-api/commit/2d80ddd94a5153428f7c417211fce5d9fa0610e9))
* **REGISTRY-1793:** Organisation | SRO application - send email (#415) ([2e7e206](https://github.com/HDRUK/safepeopleregistry-api/commit/2e7e206c5a489507027173e3046accb53a37a3ce))
* **REGISTRY-1793:** Organisation | SRO application - update organisation for get files (#414) ([51406bb](https://github.com/HDRUK/safepeopleregistry-api/commit/51406bb8822cb8024ba88e37b36b909d43c2f87e))
* **REGISTRY-1796:** Organisation | SRO application - super-admin interface (#417) ([c126f34](https://github.com/HDRUK/safepeopleregistry-api/commit/c126f34076fa5c639cfc12e617297113c92721de))
* **REGISTRY-1797:** locking / unlocking functionality (#419) ([09f67e3](https://github.com/HDRUK/safepeopleregistry-api/commit/09f67e3d916edc5932d4f442a04c8123872bce9c))
* **REGISTRY-1809:** Organisation/User admin | Filter by status is broken on pending (#406) ([30087e5](https://github.com/HDRUK/safepeopleregistry-api/commit/30087e58f97e44e9b7709e9bbe91a8782a8bf81a))
* **REGISTRY-1865:** reg issues (#421) ([fd54286](https://github.com/HDRUK/safepeopleregistry-api/commit/fd54286ffe558268088b7c31463d29710cbdc0e9))

### 🐛 Bug Fixes

* **GAT-7812:** Remove htaccess file (#416) ([e2603e4](https://github.com/HDRUK/safepeopleregistry-api/commit/e2603e42ed41b017ee23918002abef7f2a2b9a17)), closes [GAT-7812](GAT-7812)
* **REGISTRY-1234:** fix for user invite who replace research invite (#409) ([5e59882](https://github.com/HDRUK/safepeopleregistry-api/commit/5e598829a26de6fd3b16d9a343ece5c6e699f822))
* **REGISTRY-1234:** fix user invite template (#411) ([ee33839](https://github.com/HDRUK/safepeopleregistry-api/commit/ee3383927d7bffb67b014212589a5ec7e5fad1ff))
* **REGISTRY-1768:** Custodian/User & Organisation Statuses into grey box (#394) ([7c2995f](https://github.com/HDRUK/safepeopleregistry-api/commit/7c2995fe29dcf9a22eae365de796b5c331e5a180))
* **REGISTRY-1768:** Custodian/User & Organisation Statuses into grey box revert (#396) ([655690e](https://github.com/HDRUK/safepeopleregistry-api/commit/655690e5f0609ab193f3cb456d487cd584274788))
* **REGISTRY-1773:** Custodian | How do we make & claim Data Custodian accounts (#422) ([fb7e343](https://github.com/HDRUK/safepeopleregistry-api/commit/fb7e34358a27a90adeb3eec54b7cad23386252fa))
* **REGISTRY-1773:** update for invite custodian by super-admin (#423) ([60ca9f3](https://github.com/HDRUK/safepeopleregistry-api/commit/60ca9f3801374b43378c3a28e4af2056d424c551))
* **REGISTRY-1797:** an organisation can invite users (#424) ([bbc736c](https://github.com/HDRUK/safepeopleregistry-api/commit/bbc736c90eba30dcf3c93caa10ecdba9c23cff28))
* **REGISTRY-1867:** add approval state to project payload (#413) ([db52834](https://github.com/HDRUK/safepeopleregistry-api/commit/db528342a9e7cd0c436fe818fb4ba8aabc5caa3c))

## [1.9.0](https://github.com/HDRUK/safepeopleregistry-api/compare/v1.8.0...v1.9.0) (2025-08-26)

### ✨ Features

* **REGISTRY-1673:** End point for Custodian/User & Organisation Statuses into grey (#388) ([f737b2e](https://github.com/HDRUK/safepeopleregistry-api/commit/f737b2e478c2381135d38c3ae105baf93e737765))
* **REGISTRY-1751:** Adds endpoint that creates an organisation with a … (#390) ([ea1134f](https://github.com/HDRUK/safepeopleregistry-api/commit/ea1134feef44ef1753505a997cb29b26794a32fe))
* **REGISTRY-1757:** adds in system_approved flag for organisations (#391) ([5771832](https://github.com/HDRUK/safepeopleregistry-api/commit/5771832321409ffabb6ae0f774f9cbdd5d953a97))
* **REGISTRY-1757:** Adds system_approved flag to Organisations ([4169782](https://github.com/HDRUK/safepeopleregistry-api/commit/4169782c45f1b6d91c6956983a57857be4e3e226))
* **REGISTRY-1757:** Adds system_approved flag to the API to govern if an Organisation has been approved for system use overall ([54c798f](https://github.com/HDRUK/safepeopleregistry-api/commit/54c798f9ca22c972a7ebb7a35f95f45eeccef6e0))
* **SOURSD-1751:** Adds endpoint that creates an organisation with a linked user with minimal data provided ([321b2ec](https://github.com/HDRUK/safepeopleregistry-api/commit/321b2eca36014f9eb26d604d8fd853826513e7e5))

### 🐛 Bug Fixes

* **REGISTRY-1622:** Organisation invites Delegate email: “by [[organisations.organisation_name]].” is displaying blank (#389) ([68a59eb](https://github.com/HDRUK/safepeopleregistry-api/commit/68a59eb465ea81487f4eda5137750fa7a50a363c))
* **REGISTRY-1666:** Don't create keycloak user if user has been invited (#392) ([9d71a80](https://github.com/HDRUK/safepeopleregistry-api/commit/9d71a805aba980e15e250b8cec99b5a5b614f13d))

## [1.8.0](https://github.com/HDRUK/soursd-api/compare/v1.7.0...v1.8.0) (2025-07-31)

### ✨ Features

* **REGISTRY-1297:** Bugfix/registry 1297 (#376) ([9fc0580](https://github.com/HDRUK/soursd-api/commit/9fc05805e3172c6ce577bd7ff5ba44c023577d2e))
* **REGISTRY-1316:** Job to merge accounts (#381) ([25c03fc](https://github.com/HDRUK/soursd-api/commit/25c03fcccd7eb8164fb07b6e76c67da607c7e544))
* **REGISTRY-1601:** Update statuses (#380) ([c9040d5](https://github.com/HDRUK/soursd-api/commit/c9040d5725ebcb13fa02360631c760cac6b9f69e))

### 🐛 Bug Fixes

* **REGISTRY-1234:** Claim an account that is already created (#378) ([f384d85](https://github.com/HDRUK/soursd-api/commit/f384d85ef99438bb3d49e701716ddbe76cd06d37))
* **REGISTRY-1261:** add, update, remove subsidiary (#370) ([8ba551c](https://github.com/HDRUK/soursd-api/commit/8ba551cfa3fa5b01dcf93800e69689af301fec45))
* **REGISTRY-1325:** Dont duplicate affiliation creation upon register (#379) ([2d7fb6e](https://github.com/HDRUK/soursd-api/commit/2d7fb6e06165c7fcd40af95718dc64f95cd53e16))
* **REGISTRY-1576:** Add protection when seeding for user history (#375) ([dc36bc4](https://github.com/HDRUK/soursd-api/commit/dc36bc49412a18c21b9af591f3db64e7d0880264))
* **REGISTRY-1576:** create affiliation upon invite (#377) ([81b8814](https://github.com/HDRUK/soursd-api/commit/81b8814349563e683a5df224bbd1ef57b9c1a8e5))
* **REGISTRY-1579:** Custodians | Organisations - status in user list (#386) ([07fa219](https://github.com/HDRUK/soursd-api/commit/07fa219f58ce7ba7cbaad2d687dadd3885171f9b))
* **REGISTRY-1602:** update statuses and added affiliation state (#384) ([2b4574c](https://github.com/HDRUK/soursd-api/commit/2b4574cf0e406c3acc3dc76c975ee8dd348fc352))
* **SOURSD-1234:** update UpdateActionNotifications and AppServiceProvider (#368) ([6754b54](https://github.com/HDRUK/soursd-api/commit/6754b54d9f442bf4d7c93397af66c973ad555b67))
* **SOURSD-1469:** add return for model state on Projects (#373) ([099ca11](https://github.com/HDRUK/soursd-api/commit/099ca119b2d1dcdd93abba23fbc96a041f1895e3))
* **SOURSD-1477:** wrong group name (#372) ([4cba74b](https://github.com/HDRUK/soursd-api/commit/4cba74b281087b00224a4a36dd09fdae346e3137))
* **SOURSD-1490:** Fix for return types (#369) ([6b9e092](https://github.com/HDRUK/soursd-api/commit/6b9e092e7029e5243b839f6de309f0be3f67f8f8))
* **SOURSD-1496:** Updates for project users (#371) ([c39acd7](https://github.com/HDRUK/soursd-api/commit/c39acd7c3562e153c69d544b666744d648200b2e))
* **SOURSD-1600:** Move to a static method for transitions (#367) ([278619e](https://github.com/HDRUK/soursd-api/commit/278619e2bf36bf8fa162fb45677ad66342998fb1))

## [1.7.0](https://github.com/HDRUK/soursd-api/compare/v1.6.0...v1.7.0) (2025-07-11)

### ✨ Features

* **SOURSD-1254:** Temp seed fix (#321) ([6539e76](https://github.com/HDRUK/soursd-api/commit/6539e765c084ecc54bd463d9644fad1b98075690))
* **SOURSD-1301:** new route for org affiliations (#354) ([a7be9e9](https://github.com/HDRUK/soursd-api/commit/a7be9e9958f461d62d0a64a60333dffe6b389573))
* **SOURSD-1585:** Organisations | Profile | Data security compliance - upload and download file (#365) ([829f099](https://github.com/HDRUK/soursd-api/commit/829f0994137349a84fbe3db6f4bb5ea24e68f364))
* **SOURSD-1590:** filter by affiliation status (#364) ([8769e94](https://github.com/HDRUK/soursd-api/commit/8769e9412afca71addf7727db1a35ed10eb28e88))

### 🐛 Bug Fixes

* **GAT-1234:** add redis in docker (#351) ([39d895c](https://github.com/HDRUK/soursd-api/commit/39d895c1693aae3abfe0cdc5cd64e6b2dc103c9d)), closes [GAT-1234](GAT-1234)
* **SOURSD-1462:** Fixes for getting organisation users (#355) ([4c402f6](https://github.com/HDRUK/soursd-api/commit/4c402f6149ba9fbff4e05d74883c314f9fec23c6))
* **SOURSD-1471:** Update actions when training complete (#353) ([1de5333](https://github.com/HDRUK/soursd-api/commit/1de53338da2bece1fcd48c85af2e01fc8b57f237))
* **SOURSD-1478:** update completion upon seeding.. (#361) ([2a8936b](https://github.com/HDRUK/soursd-api/commit/2a8936be5b86dac857b1451aba00deb4680bc84e))
* **SOURSD-1490:** Remove old rules and change action logs   (#363) ([6e8ec09](https://github.com/HDRUK/soursd-api/commit/6e8ec09a57482d64e323b868ce78fb1f0f846c5e))
* **SOURSD-1563:** fix for adding contacts (#362) ([a888cc5](https://github.com/HDRUK/soursd-api/commit/a888cc5ca421bbbebcd1a3cd58774746372f6b54))
* **SOURSD-1566:** filter project users (#359) ([22d85b5](https://github.com/HDRUK/soursd-api/commit/22d85b58aa3e68eb29f283597e2d4cbd3c7013de))
* **SOURSD-1568:** Fix filtering on dates  (#357) ([f47620e](https://github.com/HDRUK/soursd-api/commit/f47620e66624ece790de9d1c343ce7a2487f7914))
* **SOURSD-1571:** remove PHO if no others on the project with that affiliation (#358) ([ffa6ab8](https://github.com/HDRUK/soursd-api/commit/ffa6ab8f6d86c0ef6f92264eefaa459b190344db))
* **SOURSD-1577:** filter (#360) ([b0df6f6](https://github.com/HDRUK/soursd-api/commit/b0df6f672acdc218616aeca0aa9c4898843f0a32))
* **SOURSD-1592:** fix for getting spawing emails working again (#356) ([a1199c0](https://github.com/HDRUK/soursd-api/commit/a1199c02f6a50121d39faec68d7b8d01133b897c))

## [1.6.0](https://github.com/HDRUK/soursd-api/compare/v1.5.1...v1.6.0) (2025-07-03)

### ✨ Features

* **SOURSD-1007:** add string when registering new organisation (#323) ([9d77061](https://github.com/HDRUK/soursd-api/commit/9d770614e69b3ee92b7f42529ded2bb99f16b3eb))
* **SOURSD-1018:** Stop notifications firing twice (#324) ([9020c10](https://github.com/HDRUK/soursd-api/commit/9020c1033897a081be376cbfd9388434051145f9))
* **SOURSD-1023:** create identity when doing veriff (#328) ([a9fecac](https://github.com/HDRUK/soursd-api/commit/a9fecac64f9823aad24cf680c23534ad02f1e35d))
* **SOURSD-1292:** workflow endpoint ([fdf12a2](https://github.com/HDRUK/soursd-api/commit/fdf12a2755be37db879da51d427cc469eaccac7e))
* **SOURSD-1292:** workflow endpoint (#334) ([71a6918](https://github.com/HDRUK/soursd-api/commit/71a691887787c20c8a32478a8e617689601df5cb))
* **SOURSD-1293:** org transitions (#338) ([f274923](https://github.com/HDRUK/soursd-api/commit/f274923cf036209b80e2316ea90ec4f20a443e89))
* **SOURSD-1322:** Factors in simple emailing mode for invites to not always create unclaimed accounts ([55e09f7](https://github.com/HDRUK/soursd-api/commit/55e09f731e0ed49d3f041da14b1fbf3250831da4))
* **SOURSD-1322:** Factors in simple emailing mode for invites to not… (#325) ([7a747c8](https://github.com/HDRUK/soursd-api/commit/7a747c8e3542ed7d3eaf976f8a62fabbd446006b))
* **SOURSD-1350:** Refactoring project user custodian approval (#332) ([432b523](https://github.com/HDRUK/soursd-api/commit/432b52369bfdef00af87f4096c502dfe31f05d35))
* **SOURSD-1371:** Replace UserAuditLog with ActivityLog (#335) ([b0d65b2](https://github.com/HDRUK/soursd-api/commit/b0d65b24391e2403896a4ae2c039281e304ed0a3))
* **SOURSD-1373:** Rework organisation approvals (#333) ([020e50d](https://github.com/HDRUK/soursd-api/commit/020e50d2c483006006429144382a3db5c4746552))
* **SOURSD-1408:** Bug fixes for list views of project users and project organisations (#340) ([f577d71](https://github.com/HDRUK/soursd-api/commit/f577d7155108fa49df6e8db4b81466bb275e5da0))
* **SOURSD-1409:** optimisation of logging (#344) ([e796f7e](https://github.com/HDRUK/soursd-api/commit/e796f7e27717a2299408a4ffce45954e6805bd33))
* **SOURSD-1415:** Adds in feature flags to system_config table to co… (#336) ([32e9532](https://github.com/HDRUK/soursd-api/commit/32e9532cd4d7c6cd6d7b992635229f26fb730e1b))
* **SOURSD-1415:** Adds in feature flags to system_config table to control what is made available in the FE - Temporary fix ([cb2480e](https://github.com/HDRUK/soursd-api/commit/cb2480e8c1ba2b6ffd46d5be49a057c81b5d611c))
* **SOURSD-1416:** Changes needed for activity  (#337) ([1a3ffe6](https://github.com/HDRUK/soursd-api/commit/1a3ffe69812ec966839cdbc798bbbe61f935c7eb))
* **SOURSD-1425:**  Removing old code (#342) ([56b013e](https://github.com/HDRUK/soursd-api/commit/56b013ecc6060fa4cd5c87d63080b7ace900b2f3))
* **SOURSD-1426:** Further swagger annotation updates (#345) ([512033e](https://github.com/HDRUK/soursd-api/commit/512033e7630f60f556d08691e58fd433fe05e0dc))
* **SOURSD-1426:** update swagger annotations 1 ([07b3553](https://github.com/HDRUK/soursd-api/commit/07b3553b15175ff7df81f4c37494ae2164eb6692))
* **SOURSD-1426:** Updates models to make consistent use of Swagger schemas (#343) ([e0c01b5](https://github.com/HDRUK/soursd-api/commit/e0c01b549e09a48594ffe84ebc958d204d0a9809))
* **SOURSD-1433:** Remove registry has affiliation (#341) ([67edb37](https://github.com/HDRUK/soursd-api/commit/67edb375d79d845116fa04fb24b4e6230a9cf77f))
* **SOURSD-1435:** Affiliation notifications and activity (#347) ([7af77d0](https://github.com/HDRUK/soursd-api/commit/7af77d06d0891d180296a1e0fd813f544c4aa582))
* **SOURSD-826:** Notification on affiliation create/update/delete (#322) ([3315684](https://github.com/HDRUK/soursd-api/commit/33156842d71c55f9247240f1fd3ab6ab469b0cd0))
* **SOURSD-876-2:** Upgrading to Laravel v12 - first commit ([4c0c876](https://github.com/HDRUK/soursd-api/commit/4c0c8760017d67ce5f42233ffe90f7d8a41aa7a6))
* **SOURSD-876:** Migrate to Laravel v12 ([084e159](https://github.com/HDRUK/soursd-api/commit/084e159f622e4b93f6ded309ce3a99e8422007aa))
* **SOURSD-876:** Migrates Registry to Laravel v12.x (#329) ([c5c29f0](https://github.com/HDRUK/soursd-api/commit/c5c29f09756f622c8ddc559111baffe56fb03701))
* **SOURSD-876:** Upgrades to Laravel v11.x (#326) ([2c593fb](https://github.com/HDRUK/soursd-api/commit/2c593fb7df3494b604c6103876c264ddf884d467))
* **SOURSD-970:** Fixes for custodian project users (#327) ([11d16a2](https://github.com/HDRUK/soursd-api/commit/11d16a2b7162422099a50aa9bcab12f1fe74eb0f))

### 🐛 Bug Fixes

* **SOURSD-1058:** migration null (#349) ([8d70852](https://github.com/HDRUK/soursd-api/commit/8d708524c0d67935935dcfb3f6525be6e95d16b4))
* **SOURSD-1343:** Fix saving of rules  (#339) ([2341afa](https://github.com/HDRUK/soursd-api/commit/2341afa8f97a32d10a22265798342220803176eb))
* **SOURSD-1426:** Removes old edit methods as no longer supported in … (#346) ([5790b96](https://github.com/HDRUK/soursd-api/commit/5790b967d4b23cdf1a216e88e667600b1b85cc92))
* **SOURSD-1426:** Removes old edit methods as no longer supported in Lv12 - BREAKING CHANGEgit add . ([c5332ed](https://github.com/HDRUK/soursd-api/commit/c5332edeeb53ac3fc553461ea4a3222555000049))
* **SOURSD-876:** Updates laravel to v11 ([83338b5](https://github.com/HDRUK/soursd-api/commit/83338b58e5b40662bc52b2f6080f977ef2b9e68c))
* **SOURSD-880:** ClamAV service path was calling incorrect var ([0fa4360](https://github.com/HDRUK/soursd-api/commit/0fa4360c2cbac26d4afbd025c0835c6fe547dbe5))
* **SOURSD-880:** ClamAV service path was calling incorrect var (#320) ([7d1e557](https://github.com/HDRUK/soursd-api/commit/7d1e55709da62e542653159e23ee513e6cd99549))
* **SOURSD-880:** replaces frankenphp with roadrunner for finer tuning of resource limits ([353981c](https://github.com/HDRUK/soursd-api/commit/353981c1e07d06bd7447c02880e15f3ef2c3587a))
* **SOURSD-880:** replaces frankenphp with roadrunner for finer tuning… (#330) ([fe65012](https://github.com/HDRUK/soursd-api/commit/fe65012e98e7b8b1de7f4bf72b069248b7406e33))

## [1.5.1](https://github.com/HDRUK/soursd-api/compare/v1.5.0...v1.5.1) (2025-05-22)

### 🐛 Bug Fixes

* **SOURSD-1113:** Implement Validation Check controller (#306) ([ee49c76](https://github.com/HDRUK/soursd-api/commit/ee49c760b44cc2147c46c39c44be64c35ae50df7))
* **SOURSD-1113:** Quick fixes for swagger (#316) ([6a34eb5](https://github.com/HDRUK/soursd-api/commit/6a34eb53445641ac8629d9626332ae19962156e1))
* **SOURSD-880:** Attempt to reduce uncleared memory and adds logging … (#314) ([a87dfc9](https://github.com/HDRUK/soursd-api/commit/a87dfc9f41f9b68fdef4e1c7cdd0d205ffa678b4))
* **SOURSD-880:** Attempt to reduce uncleared memory and adds logging for strange certs issue in dev env ([c874fa2](https://github.com/HDRUK/soursd-api/commit/c874fa2bef171db3f51df91c6ccf0cf33a010e96))
* **SOURSD-880:** Remap keycloak ids after new environment deployed ([dbd6568](https://github.com/HDRUK/soursd-api/commit/dbd6568e247f7e5b277a6947e86ec8cb5dc398b7))
* **SOURSD-880:** Remap keycloak ids after new environment deployed (#312) ([7656b36](https://github.com/HDRUK/soursd-api/commit/7656b36d8cf6f50b63efd1159286b277652d2a8e))
* **SOURSD-880:** Round 10 of trying to get to the bottom of this memo… (#315) ([34a49fb](https://github.com/HDRUK/soursd-api/commit/34a49fb5cae0ea33961b5cdbb72a5411ed7551d8))
* **SOURSD-880:** Round 10 of trying to get to the bottom of this memory leak in dev ([7633109](https://github.com/HDRUK/soursd-api/commit/763310934b634942008bf7d1ef00e6c5b162f1e5))

## [1.5.0](https://github.com/HDRUK/soursd-api/compare/v1.4.0...v1.5.0) (2025-05-19)

### ✨ Features

* **SOURSD-1002:** delegates not inviting (#282) ([828e2db](https://github.com/HDRUK/soursd-api/commit/828e2db74227628ae9ae9196827b8aa47c6a7441))
* **SOURSD-1021:** Implement authorisation checks (#284) ([84e4ef7](https://github.com/HDRUK/soursd-api/commit/84e4ef715b7d6e389cc36e889e0f0285d5380cc2))
* **SOURSD-1023:** Implement generic backend webhook receiver for IDVT partners (#292) ([fa69107](https://github.com/HDRUK/soursd-api/commit/fa69107b1318d314e3d64164b8bde3db0da754ae))
* **SOURSD-1023:** Implementation of IDVT supplier callbacks/webhook receivers ([6f9b14e](https://github.com/HDRUK/soursd-api/commit/6f9b14e477af355dcc850a827eaeca692a50e9a6))
* **SOURSD-1026:** add custodian projects (#288) ([96241bc](https://github.com/HDRUK/soursd-api/commit/96241bcfc86ce509262802dd7a57c0cf636515f6))
* **SOURSD-1032:** Ensure ORCID Scanner is running at correct points … (#297) ([709b1b1](https://github.com/HDRUK/soursd-api/commit/709b1b132de061103ab16127aee4cac433770950))
* **SOURSD-1032:** Ensure ORCID Scanner is running at correct points of user creation ([5126b14](https://github.com/HDRUK/soursd-api/commit/5126b14cc918b258bcf92de7da4e9c99d8e9f459))
* **SOURSD-1079:** Implement debug strings for Custodian testing.  (#301) ([71b7205](https://github.com/HDRUK/soursd-api/commit/71b7205889e439784355357486ee25216342c209))
* **SOURSD-829:** Rework approvals (#299) ([cc19fba](https://github.com/HDRUK/soursd-api/commit/cc19fba9875a88e1c995771b0625a7064750d9d3))
* **SOURSD-880:** Cleans up usage of http calls that potentially lead to memory leaks confirmed by issue raised against Laravel 10. ([3cf8bc6](https://github.com/HDRUK/soursd-api/commit/3cf8bc6b6ef7ec986386004641c49d765f9ef5dc))
* **SOURSD-880:** Cleans up usage of http calls that potentially lead… (#307) ([fb11299](https://github.com/HDRUK/soursd-api/commit/fb11299a82cb86d29b78e171072ad8f53b37056e))
* **SOURSD-953:** Adding in a fake endpoint for user history (#277) ([3939d0a](https://github.com/HDRUK/soursd-api/commit/3939d0abbb64e39669c0471b53f877045d7842d5))
* **SOURSD-955:** move rules payload (#276) ([c223b9a](https://github.com/HDRUK/soursd-api/commit/c223b9a4ae752a0238613936b7c308373d34ceaf))
* **SOURSD-956:** get user projects for custodian (#267) ([2917ce2](https://github.com/HDRUK/soursd-api/commit/2917ce27afca4c2638a93bec1016c33503eba14a))
* **SOURSD-972:** custodian organisations and projects (#269) ([46c4fb3](https://github.com/HDRUK/soursd-api/commit/46c4fb3963e196b64cac46938ba9be30509ddd60))
* **SOURSD-973:** Backend component for issuing read request notifications by custodians ([366575c](https://github.com/HDRUK/soursd-api/commit/366575c5966551ab04893ee1336441b1ca13bb10))
* **SOURSD-973:** Changes for custodian to issue read requests for individual SOURSD data (#272) ([f1b3218](https://github.com/HDRUK/soursd-api/commit/f1b3218d07eb75149c7c2ae2f5dee0f85057efc7))
* **SOURSD-973:** Further improvements to the requesting access to us… (#286) ([f91dc19](https://github.com/HDRUK/soursd-api/commit/f91dc192699404cbf2cdda4679ec864a79661e26))
* **SOURSD-973:** more tweaks to offer more information to end-user ([1388aab](https://github.com/HDRUK/soursd-api/commit/1388aab8e937d6f54d3b87cf7a19d1d55149afe9))
* **SOURSD-973:** RegistryReadRequests ([0751b37](https://github.com/HDRUK/soursd-api/commit/0751b3770fcaf0a35b74584de7f08ec2beebd86e))
* **SOURSD-979:** Implement validation logs for custodian organisation (#275) ([6f1a664](https://github.com/HDRUK/soursd-api/commit/6f1a664f2d7a52507a86b8ad861fcdb02a7834da))
* **SOURSD-990:** people (#271) ([08ce7b7](https://github.com/HDRUK/soursd-api/commit/08ce7b77690800e6db719043ab498b361284a13d))
* **SOURSD-994:** custodian user by custodian (#279) ([e106a13](https://github.com/HDRUK/soursd-api/commit/e106a130dcc5ae1feecbc1f18cc0f5f866040639))

### 🐛 Bug Fixes

* **SOURSD-1000:** custodian user invite ([f71276e](https://github.com/HDRUK/soursd-api/commit/f71276e3e1718d42bc48b5f9a0a5316f93e2904e))
* **SOURSD-1000:** custodian user invite (#280) ([7e061b2](https://github.com/HDRUK/soursd-api/commit/7e061b2a9dd86a873a99cba8c1a90324898744f7))
* **SOURSD-1001:** Change default values in ProjectDetail to be arrays rather than objects. (#281) ([2d2f6f4](https://github.com/HDRUK/soursd-api/commit/2d2f6f430961740fba08050c0ce5abc29297a289))
* **SOURSD-1002:** delegate invite breaking ([8227c2d](https://github.com/HDRUK/soursd-api/commit/8227c2d3bfc655929a1f9fc1b25f9857ad9b80d6))
* **SOURSD-1010:** Get filters working  (#304) ([a77ffac](https://github.com/HDRUK/soursd-api/commit/a77ffac7a190975ccd8928dc45429886dcdc1ff1))
* **SOURSD-1011:** add migration to have a virtual name (#303) ([7fd29e0](https://github.com/HDRUK/soursd-api/commit/7fd29e01a35efe986fe582392c227044471faf03))
* **SOURSD-1016:** add affiliations to org users endpoint (#289) ([767d6e4](https://github.com/HDRUK/soursd-api/commit/767d6e4fcff58cd2666b6e1254b1639c5d039507))
* **SOURSD-1023:** Final backend updates to receive Veriff webhooks (#294) ([2c9fba4](https://github.com/HDRUK/soursd-api/commit/2c9fba44e3a1c7eaa8e8c2eaf628b491317f01b7))
* **SOURSD-1023:** further debugging and need to handle the submit act… (#293) ([064b2c2](https://github.com/HDRUK/soursd-api/commit/064b2c2127bcfd2d208c174e6a921e71711ecde2))
* **SOURSD-1023:** further debugging and need to handle the submit action which is tripping it up ([de85231](https://github.com/HDRUK/soursd-api/commit/de85231d4f9886366f2ed9f182e9c30b18bba5e4))
* **SOURSD-1034:** fix for form error (#305) ([5f5045e](https://github.com/HDRUK/soursd-api/commit/5f5045e6f2b9456941f8a19e71ba9ea55083f2e5))
* **SOURSD-1039:** small change to stop primary contact changing (#302) ([18cad03](https://github.com/HDRUK/soursd-api/commit/18cad03844f0c7747db4ca9bac0978bc970dd17d))
* **SOURSD-1043:** Prevents multiple receivers being created for the s… (#298) ([db20c45](https://github.com/HDRUK/soursd-api/commit/db20c45fec22f36cad4fac299ffb9bf0245ac9f1))
* **SOURSD-1043:** Prevents multiple receivers being created for the same webhook - also linting fixes ([2e548fe](https://github.com/HDRUK/soursd-api/commit/2e548fe6ecb3b7a938d5d9f61ef234ba00c910d6))
* **SOURSD-1044:** Seed rules properly  (#291) ([30f8b5f](https://github.com/HDRUK/soursd-api/commit/30f8b5ffb91cc70bf690897baa05122d1391e8c5))
* **SOURSD-717:** Implements GCS config for GCP buckets to accomodate … (#296) ([6154628](https://github.com/HDRUK/soursd-api/commit/6154628c53c71080af3a181524fdb151e743e341))
* **SOURSD-717:** Implements GCS config for GCP buckets to accomodate file uploads in clamav. Also removes a reference to a rule that didn't exist which was killing the application ([997b899](https://github.com/HDRUK/soursd-api/commit/997b8996bf2d15d7c511df3b04f2fbbd7a15ba56))
* **SOURSD-880:** Adds in logging to see what is causing the memory le… (#309) ([fc89eff](https://github.com/HDRUK/soursd-api/commit/fc89effa5b9de207376765d434af7d41cb9ce395))
* **SOURSD-880:** Adds in logging to see what is causing the memory leak, hopefully ([3efa887](https://github.com/HDRUK/soursd-api/commit/3efa8873a604553031d3230e4b6afc7b5003312e))
* **SOURSD-880:** Implement some config around frankenphp to ensure se… (#311) ([ee258da](https://github.com/HDRUK/soursd-api/commit/ee258da5031a44875347b09145ed924222dbcaf3))
* **SOURSD-880:** Implement some config around frankenphp to ensure sensible limits ([d8ad8ec](https://github.com/HDRUK/soursd-api/commit/d8ad8ec161d11a1a647f32048f888ac9f762fe6b))
* **SOURSD-880:** Possible unbound memory growth detected, with the si… (#308) ([d7eb08a](https://github.com/HDRUK/soursd-api/commit/d7eb08aef6a2d564c64177eeebc56d76f9a567e5))
* **SOURSD-880:** Possible unbound memory growth detected, with the size of the eloquent models and relationships being pulled. Adding this to see if it clears down allocations, as laravel doesn't automatically release memory when chunking ([effb6ef](https://github.com/HDRUK/soursd-api/commit/effb6ef261096725a81a2d400111ebdc49b56adb))
* **SOURSD-880:** unable to find previous logs. writing to db instead ([3976b36](https://github.com/HDRUK/soursd-api/commit/3976b367a7b72bfe7d4adc70865b927a21872d82))
* **SOURSD-880:** unable to find previous logs. writing to db instead (#310) ([8127723](https://github.com/HDRUK/soursd-api/commit/812772334bb3281583a19b70d32e59c008c7b0b9))
* **SOURSD-900:** correct project status (#283) ([41465b5](https://github.com/HDRUK/soursd-api/commit/41465b54e996c80a7318d769f2b4012a0b44dfd4))
* **SOURSD-919:** Get delegate email flow working (#259) ([c82e3df](https://github.com/HDRUK/soursd-api/commit/c82e3df26d5d0090202f0c6e622df409344af8ce))
* **SOURSD-922:** Custodian User invite fix (#278) ([2b96298](https://github.com/HDRUK/soursd-api/commit/2b96298fd3ca3090384a0be6de53c941466546fb))
* **SOURSD-922:** Prevent crash when resending invites (#290) ([81b3aaa](https://github.com/HDRUK/soursd-api/commit/81b3aaa9b16d1d23e4d7e5e50d23930a29b8574d))
* **SOURSD-978:** Fixes for validation log  (#274) ([236fc68](https://github.com/HDRUK/soursd-api/commit/236fc6812a46ef47d2e7a43b3d0ac904f420b6ba))
* **SOURSD-983:** Fixes file upload after changes to clamav supporting multiple services. Now SOURSD sends its service path for file download/scanning correctly ([6a62801](https://github.com/HDRUK/soursd-api/commit/6a62801c5a8c7b4e6fb4858271e383c6213ffa7b))
* **SOURSD-983:** Fixes file upload after changes to clamav supporting… (#287) ([3b1c289](https://github.com/HDRUK/soursd-api/commit/3b1c28918fa197dc526d5fc6314218a23e6c319e))
* **SOURSD-988:** swagger (#273) ([67c3530](https://github.com/HDRUK/soursd-api/commit/67c35303b3bbf7cd29ccc8ce085236dfd728d9d0))
* strip out user ([c2d1369](https://github.com/HDRUK/soursd-api/commit/c2d1369183b7e9509992ce57c973a06b0f3426dc))

## [1.4.0](https://github.com/HDRUK/soursd-api/compare/v1.3.0...v1.4.0) (2025-04-11)

### ✨ Features

* **SOURSD-912:** Adds rule engine into core code, as go rules failed miserably when running against a full payload of users ([1f8faad](https://github.com/HDRUK/soursd-api/commit/1f8faadb6af2ba140c9337a8107a13f7a2afe988))
* **SOURSD-912:** Further implementation of rules engine and custom rule sets ([6266857](https://github.com/HDRUK/soursd-api/commit/626685713ac5ca614f5595d28d854822c3a489c3))
* **SOURSD-912:** Replace gorules with custom implementation for easier and extended use ([a63bfba](https://github.com/HDRUK/soursd-api/commit/a63bfba74b3d205a1ed641eb3339435ebb4a1d8e))
* **SOURSD-931:** update state (#254) ([ad9ebe1](https://github.com/HDRUK/soursd-api/commit/ad9ebe1ed50d5eb9ef19cb99a21051e58c043a5f))
* **SOURSD-944:** Implements the rest of the default rules ([fb8889e](https://github.com/HDRUK/soursd-api/commit/fb8889e5c10bc6101eed75c62272b862f3eef14d))
* **SOURSD-946:** Add migrate to auto build in pipeline. Remove continued prod seeder ([f5af427](https://github.com/HDRUK/soursd-api/commit/f5af427e59b693a0a9b777ed828d52ae5c507b32))
* **SOURSD-957:** Implements more security around custodian querying ([7ee816f](https://github.com/HDRUK/soursd-api/commit/7ee816f089f55717c93ee2c03d13b3f6bbd1b48c))

### 🐛 Bug Fixes

* **SOURSD-948:** Corrects a bug which accidentally applied custodian rules to organisational requests ([def7af1](https://github.com/HDRUK/soursd-api/commit/def7af11d5aeb01b637346cbb64daa1002b644f1))
* **SOURSD-952:** Apply regId filter in logic of endpoint. (#266) ([d139e53](https://github.com/HDRUK/soursd-api/commit/d139e53299538c225bdea23e8ac717cc29e6dbbd))
* **SOURSD-962:** Attempt to fix keycloak timeout in dev env ([85c0fed](https://github.com/HDRUK/soursd-api/commit/85c0fed3761bd91094c45d5845f424239b655bb0))

## [1.3.0](https://github.com/HDRUK/soursd-api/compare/v1.2.0...v1.3.0) (2025-03-31)

### ✨ Features

* **SOURSD-910:** Create keycloak users and send email to log in and change password ([29c9194](https://github.com/HDRUK/soursd-api/commit/29c9194e22db8a53634731805f1e020648a13ed8))
* **SOURSD-910:** ensure soursd users are created in keycloak if not present ([bcd1550](https://github.com/HDRUK/soursd-api/commit/bcd1550734a59f06b6496ffd4fa670fc30f20156))
* **SOURSD-911:** Adds automated ability to query custodian request and apply rules engine config to the response ([963c5f0](https://github.com/HDRUK/soursd-api/commit/963c5f04703ad767c1707596efccf6e07aa5cc92))
* **SOURSD-911:** Adds mechanism for pulls of user endpoint to take into account custodian config and parse rulesets in real-time ([addfe89](https://github.com/HDRUK/soursd-api/commit/addfe89d49f459203f5b3798e99147c091f5187a))

### 🐛 Bug Fixes

* **SOURD-923:** debugging login issues ([e4bc4af](https://github.com/HDRUK/soursd-api/commit/e4bc4af3c201278baa957c1be7d808a410dc8d8d))
* **SOURSD-930:** accept same state for projects (#249) ([9d34cac](https://github.com/HDRUK/soursd-api/commit/9d34cac90ea80f7391fb92881124924e15bf7384))

## [1.2.0](https://github.com/HDRUK/soursd-api/compare/v1.1.0...v1.2.0) (2025-03-25)

### ✨ Features

* **SOURSD-808:** change access type project detail field (#240) ([2f02f7a](https://github.com/HDRUK/soursd-api/commit/2f02f7a6baa4b2d54bed6f05261f7706d61efc2c))
* **SOURSD-923:** Adds affiliations to user listings, allows searching by user_group to filter by specific entity user types ([e2fca81](https://github.com/HDRUK/soursd-api/commit/e2fca81590ef8a14b1a7f6fb3228554b938cb915))

### 🐛 Bug Fixes

* **SOURSD-920:** Removes endpoints from keycloak guard where not necessary ([13bfe44](https://github.com/HDRUK/soursd-api/commit/13bfe4473d108fd5a1b0d13413830a8ba6f807a9))

## [1.1.0](https://github.com/HDRUK/soursd-api/compare/v1.0.0...v1.1.0) (2025-03-21)

### ✨ Features

* **SOURSD-325:** Creates live feed (monthly) from UKSA for accredited researchers ([c6f1662](https://github.com/HDRUK/soursd-api/commit/c6f166284925da4f809af4c9b421caf90289062a))
* **SOURSD-325:** initial commit of ons file fetching ([60bb823](https://github.com/HDRUK/soursd-api/commit/60bb8239054a00d050399fc5adbf1490a7107b81))
* **SOURSD-804:** add project status (#229) ([a18725b](https://github.com/HDRUK/soursd-api/commit/a18725b1c6519544b6ef7bef76b120e9bb2b49c5))
* **SOURSD-832:** Extend user, org and custodian project endpoints for table views ([cd5f0d4](https://github.com/HDRUK/soursd-api/commit/cd5f0d496d678c11ee5e8901bd1bc0000190c0eb))
* **SOURSD-832:** new route for pulling users projects ([80c628a](https://github.com/HDRUK/soursd-api/commit/80c628a50859397e95e0ddba8678f0e1002cd247))
* **SOURSD-850:** Updates Training to make use of lookup table when used with Registry entry ([18c9c3f](https://github.com/HDRUK/soursd-api/commit/18c9c3f5831ecd31ae04a4d838e6301e35e112f8))
* **SOURSD-857:** Adds Project Roles Controller and tests ([df19e5a](https://github.com/HDRUK/soursd-api/commit/df19e5aa593f3d10cd7882dcec78954b2251fc4f))
* **SOURSD-857:** fix phpstan alerts ([c46eef2](https://github.com/HDRUK/soursd-api/commit/c46eef2f039e94bbfc0c5c85617bb5f4918a5fa3))
* **SOURSD-858:** Adds new search endpoint to users controller that can search by name and professional email account tied to affiliations ([9f41e28](https://github.com/HDRUK/soursd-api/commit/9f41e28d42a966180597046072bcf7f17d0a64a7))
* **SOURSD-858:** Fixes a few loose ends ([060dc3d](https://github.com/HDRUK/soursd-api/commit/060dc3daddbd70998576b0c0148d4242197b1dbb))
* **SOURSD-859:** Implements rudimentary generic polymorphic workflow, currently for Users, Organisations and Projects only ([268bfea](https://github.com/HDRUK/soursd-api/commit/268bfea9d898c458f81330a83775453aa81f9ab5))
* **SOURSD-860:** Implement filtering on getProjectUsers function - filtering unable to be added to existing, so wrote a replacement/extra for use when filtering is needed ([9b86efb](https://github.com/HDRUK/soursd-api/commit/9b86efb2123d886e0c93d800eb88a4617262618b))
* **SOURSD-867:** endpoint to make researchers primary contacts (#208) ([b4ff9ca](https://github.com/HDRUK/soursd-api/commit/b4ff9ca64d331e0f736d8fb0926b058b459ab3a1))
* **SOURSD-870:** add temporary status field for registered or invited (#206) ([50d6c00](https://github.com/HDRUK/soursd-api/commit/50d6c00bc4514e7277b5c2368676fbf7a0df111d))
* **SOURSD-880:** Small tweaks to see if redis is causing the slow memory leak ([d1133e9](https://github.com/HDRUK/soursd-api/commit/d1133e9306f969ca6ff2d670b19f7c1606c258b0))
* **SOURSD-907:** creates logical link to training and files. Updates controller to pull files as part of training call. Adds methods of deletion of linked files when training is deleted ([3b40038](https://github.com/HDRUK/soursd-api/commit/3b400381330ee0182983f5659524866b2ad38ffd))
* **SOURSD-908:** Adds new function to retrieve users with custodian approval and their related projects ([c583cae](https://github.com/HDRUK/soursd-api/commit/c583cae1b124f65f83ffc20cf0fc4b151dd64eb2))
* **SOURSD-909:** Adds initial workflow state to user on creation ([74aa078](https://github.com/HDRUK/soursd-api/commit/74aa078217ac366f9d350da43188ddc530471c99))
* **SOURSD-913:** adds is_sro field to user table ([89a645d](https://github.com/HDRUK/soursd-api/commit/89a645d69b22e49ebba63599567e494d98737218))
* **SOURSD-915:** Adds email banner image to templates - will require a migrate:fresh ([dd5edf2](https://github.com/HDRUK/soursd-api/commit/dd5edf2e9a999bdce45d503e2b992e4262b059f6))
* **SOURSD-916:** Adds boolean flag uksa_registered to user table ([243400a](https://github.com/HDRUK/soursd-api/commit/243400adc37c8739ed4d2d24df0f53531ac469d6))

### 🐛 Bug Fixes

* 404 test ([d11253a](https://github.com/HDRUK/soursd-api/commit/d11253ad5b5bb86eba47395ef63ffc5b92db091c))
* add model state ([91514d2](https://github.com/HDRUK/soursd-api/commit/91514d2b2a3271751bc04d4c1fc2645a70a84033))
* delete ([96e4560](https://github.com/HDRUK/soursd-api/commit/96e45608ba7c7cdd74416d581b57e1352424c6ca))
* missing code ([390dff7](https://github.com/HDRUK/soursd-api/commit/390dff7cf7bf4f0e1800adffbdefe8d628c6fe7b))
* missing import ([893d298](https://github.com/HDRUK/soursd-api/commit/893d2985b11bc0e998f4d6bb4883ff0900b29383))
* **SOURSD-815:** delete training (#218) ([8b724f1](https://github.com/HDRUK/soursd-api/commit/8b724f123047024b751e3ad9def8a3f259d5b2a1))
* **SOURSD-876:** removes laravel-swoole as now abandoned and replaces with frankenphp ([8f4d9fb](https://github.com/HDRUK/soursd-api/commit/8f4d9fbe676874e3437c2ac3ed5ee1b2ae91e963))
* **SOURSD-879:** Fixes authentication not honouring tokens. Rebuilds test architecture to speed it up, a little ;) ([c20cfe3](https://github.com/HDRUK/soursd-api/commit/c20cfe3553551fa42a59645cb151e39f43ab95bf))
* **SOURSD-908:** applies searching to recently added function ([56fb22e](https://github.com/HDRUK/soursd-api/commit/56fb22edcb93a37d00aca4d181c0afe667ba0f0e))

## 1.0.0 (2025-03-03)

### ✨ Features

* **SOURSD-439:** Adds Gateway integration for DUR import when completing project details ([e008805](https://github.com/HDRUK/soursd-api/commit/e008805f173e5cf154ac669bf7bfaacd1b653f23))

### 🐛 Bug Fixes

* add new params ([c969f95](https://github.com/HDRUK/soursd-api/commit/c969f953423b9e15062d85abb94989b992b3b12e))
* add test ot patch method ([2b5396f](https://github.com/HDRUK/soursd-api/commit/2b5396f5be5f5068c23a0adfedc2a3cf94798ddb))
* ammend get trainings url ([de5b14f](https://github.com/HDRUK/soursd-api/commit/de5b14f9b444734222bf9ba70cd1796b448bdbaf))
* another sanitise ([20e795f](https://github.com/HDRUK/soursd-api/commit/20e795ffe0ba4b01e254f1cb156c4183f88f869f))
* change name in model ([07aeb7e](https://github.com/HDRUK/soursd-api/commit/07aeb7e158987abc230eeb498bbc3f73a5b6a2b2))
* conflict ([649ebf9](https://github.com/HDRUK/soursd-api/commit/649ebf95d3ebcaf867342ad4e8fedb57087f6873))
* csv upload doesn't work ([02dd3e4](https://github.com/HDRUK/soursd-api/commit/02dd3e40ebf2f30369d74eb7c6f9d7b6d02465ba))
* custodian change ([75b994e](https://github.com/HDRUK/soursd-api/commit/75b994e10e091de1ae99672137e27bbaf80692c3))
* custodian test ([5d9d522](https://github.com/HDRUK/soursd-api/commit/5d9d5226bdab862d9dd03c39106d2c34e0ccae5c))
* failed test ([8e50393](https://github.com/HDRUK/soursd-api/commit/8e50393baa0f08d387dd454fbabc8757a61c8f7b))
* function name ([9629634](https://github.com/HDRUK/soursd-api/commit/9629634fea99ac89ab7459a3fd1960817de410d4))
* getMe ([c68404b](https://github.com/HDRUK/soursd-api/commit/c68404b66876cacd2865ba1fdaa7373ef3772147))
* incorrect name ([ac9e05f](https://github.com/HDRUK/soursd-api/commit/ac9e05f8125c5e13711942e3dea60f98275f101a))
* incorrect name ([7c68e4a](https://github.com/HDRUK/soursd-api/commit/7c68e4ada4f9125874936226ad86f8274e5b2f5c))
* index query ([773d5b1](https://github.com/HDRUK/soursd-api/commit/773d5b1189734585c847f108c9606680d6708c21))
* issuer with seeder and factory ([6563f6d](https://github.com/HDRUK/soursd-api/commit/6563f6d7d9040aa78d9420f537a26005c93e5e17))
* lint ([f849bc3](https://github.com/HDRUK/soursd-api/commit/f849bc3745863f547f22734224a1789bcd7246c0))
* lint ([6066ec3](https://github.com/HDRUK/soursd-api/commit/6066ec3f4410ac76bb87ff9488ba94ae26ba098e))
* merge conflicts ([59d909f](https://github.com/HDRUK/soursd-api/commit/59d909f6fb46de4e4c89f1527b7670f3af4c97dd))
* merge issue ([3d053bd](https://github.com/HDRUK/soursd-api/commit/3d053bdb3298e539a6c32bd610d060659a9f2f8f))
* merge issues ([0918d5c](https://github.com/HDRUK/soursd-api/commit/0918d5cf3f43891476410592fdd8930b25ccf673))
* minor update ([f2bf140](https://github.com/HDRUK/soursd-api/commit/f2bf140e71d9d93a79dcc979d4ac791ae0e93153))
* minor update ([39b25bd](https://github.com/HDRUK/soursd-api/commit/39b25bd76cff4336fd31f11d1412ded3c0121ae7))
* missed calls ([4083c5b](https://github.com/HDRUK/soursd-api/commit/4083c5bf98811e56a2ddd9c492aefaed9c57dbb9))
* missed files ([4443f2e](https://github.com/HDRUK/soursd-api/commit/4443f2e9abf0124d95363c852d7d59b35e255985))
* missed fillable fields ([40e3f99](https://github.com/HDRUK/soursd-api/commit/40e3f992a6be73122ce61c3e6462f8afac073e80))
* missing code ([8618795](https://github.com/HDRUK/soursd-api/commit/86187953bcdd3c02d614c17acf38da97621aa328))
* missing comma ([caf57eb](https://github.com/HDRUK/soursd-api/commit/caf57eb9c8f7ffd755faaad0a72a5a37e627b33a))
* missing commas in swagger docs ([7ce53e2](https://github.com/HDRUK/soursd-api/commit/7ce53e2eaddd046f4b9497ce206af091fd3befe2))
* missing endpoint ([42269f9](https://github.com/HDRUK/soursd-api/commit/42269f9369ac898f8cf8830e69e7070d5f6f58fa))
* missing filter ([8430d68](https://github.com/HDRUK/soursd-api/commit/8430d6808b5e3406d88f87503f93cc90a77bb9e7))
* missing permissions route ([cedf47f](https://github.com/HDRUK/soursd-api/commit/cedf47ff46208e9cb6621d1429aaabdd6b0bcc00))
* org id and issuer id the wrong way round ([0467ce1](https://github.com/HDRUK/soursd-api/commit/0467ce18f6cb73dc1f567876df7a2bb7bb705b13))
* org id check ([3af50c6](https://github.com/HDRUK/soursd-api/commit/3af50c62709a9d79cbbaabb192992acfe0c72f09))
* permission seeder ([c1f9cc9](https://github.com/HDRUK/soursd-api/commit/c1f9cc90958d285695e3bb18ac4b77457bd0742f))
* pprocess csv test ([1cf1667](https://github.com/HDRUK/soursd-api/commit/1cf1667320f781a9942212a520ffaeb7ab18e9a8))
* pr ([83862cd](https://github.com/HDRUK/soursd-api/commit/83862cdabe8d751b80c334974d0779b1ee581dee))
* pr ([29f924f](https://github.com/HDRUK/soursd-api/commit/29f924fddad63c6d9094e9b28d8403c05a813c1f))
* pr ([eb8f390](https://github.com/HDRUK/soursd-api/commit/eb8f39063b427b6d49b2db7eae0dd7bc970555c9))
* remove cars ([42d39ea](https://github.com/HDRUK/soursd-api/commit/42d39eaa57e952756426b2c628fcf21e073b048d))
* remove orc_id from create user payload ([7228501](https://github.com/HDRUK/soursd-api/commit/7228501be95bf28670f527d69ae9f57aa37f8032))
* reset keycloak create ([3aab43e](https://github.com/HDRUK/soursd-api/commit/3aab43e86b2823e912ee3eba21fdc3efa355880b))
* resolve conflict ([9bcb05d](https://github.com/HDRUK/soursd-api/commit/9bcb05dff3bb900ccb094624d385794516fbd4e1))
* revert a change ([8b854e5](https://github.com/HDRUK/soursd-api/commit/8b854e533f78764b8fec35c35302905333a37e19))
* revert a couple of fil;es ([7a6f7b8](https://github.com/HDRUK/soursd-api/commit/7a6f7b82f6fec3680fbe721a0f307e4d38c293e7))
* revert issue ([b86bb77](https://github.com/HDRUK/soursd-api/commit/b86bb77b876ea0be91f4ccf9903caf6bd0f74538))
* something ([b886539](https://github.com/HDRUK/soursd-api/commit/b886539ed471d6e132f355994442f2e20bc8da35))
* strip out email endpoints ([8392383](https://github.com/HDRUK/soursd-api/commit/83923839073b2e10d598d5a76500515e334786c7))
* temp ([6631fd8](https://github.com/HDRUK/soursd-api/commit/6631fd8e437934bb7eb1277d2041e09a492511c5))
* test ([12cb9b1](https://github.com/HDRUK/soursd-api/commit/12cb9b128bf9e38388d5b0e739d2b4d3b3ddb9c3))
* test after merge ([84a93a5](https://github.com/HDRUK/soursd-api/commit/84a93a57d684a88231d39bcbe9dd76850165201e))
* tests ([7542ded](https://github.com/HDRUK/soursd-api/commit/7542dedaa8e3d19d720b81445475a41e6054fd90))
* tests ([ffb8773](https://github.com/HDRUK/soursd-api/commit/ffb8773c8a4f375d3e99e050f700a54912c1de77))
* tests ([7cde3f3](https://github.com/HDRUK/soursd-api/commit/7cde3f3d4f68f556924c3dbf6ce1498af83af71b))
* training endpoint by registry id ([669e181](https://github.com/HDRUK/soursd-api/commit/669e18196b9e50afde21a686c61e3cbd04a7bcab))
* uncheck in changes ([48f73d8](https://github.com/HDRUK/soursd-api/commit/48f73d8124135a41adb4bf3c5dc54a810eea7418))
* undefined var ([c10c6e6](https://github.com/HDRUK/soursd-api/commit/c10c6e6480851fe4c567dfc4bc19cb9d3cdb27c6))
* unmerged old code ([ffbf269](https://github.com/HDRUK/soursd-api/commit/ffbf269ebac453911aef51e900bd6b4314dc956a))
* unneeded code ([0918965](https://github.com/HDRUK/soursd-api/commit/09189658f7b72f1ff05e512272e35813d6e1c077))
* update invite url ([ecf7be5](https://github.com/HDRUK/soursd-api/commit/ecf7be5a9d3ddcfa5c1fd495cec382fb5d45f998))
* update routes ([fbf584b](https://github.com/HDRUK/soursd-api/commit/fbf584b8bafb56c355c8f515b32342f3c8cbb5df))
* update seeders ([7460d8a](https://github.com/HDRUK/soursd-api/commit/7460d8aea0ee893501ecb69cdfc40bff6dbb5904))
* update var name ([18eb732](https://github.com/HDRUK/soursd-api/commit/18eb7323378b44260d803e83a555e5556e073ef1))
* users test ([3d8ed66](https://github.com/HDRUK/soursd-api/commit/3d8ed66990d9370068e0249883f150d5fb7b05e1))
* wrong fields name ([2e3e379](https://github.com/HDRUK/soursd-api/commit/2e3e379db205d62d2f10ec396a545c30683fb1b8))
