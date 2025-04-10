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
