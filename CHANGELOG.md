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
