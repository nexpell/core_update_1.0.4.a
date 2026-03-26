-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Erstellungszeit: 25. Mrz 2026 um 15:44
-- Server-Version: 10.6.23-MariaDB-0ubuntu0.22.04.1-log
-- PHP-Version: 7.4.33-nmm8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `d0453787`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `backups`
--

CREATE TABLE `backups` (
  `id` int(11) NOT NULL,
  `filename` text NOT NULL,
  `description` text DEFAULT NULL,
  `createdby` int(11) NOT NULL DEFAULT 0,
  `createdate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `banned_ips`
--

CREATE TABLE `banned_ips` (
  `banID` int(11) NOT NULL,
  `ip` varchar(45) NOT NULL,
  `deltime` datetime NOT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `userID` int(11) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `captcha`
--

CREATE TABLE `captcha` (
  `hash` varchar(255) NOT NULL,
  `captcha` int(11) NOT NULL DEFAULT 0,
  `deltime` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `captcha`
--

INSERT INTO `captcha` (`hash`, `captcha`, `deltime`) VALUES
('0017264317b82d714d0017fcbae1ae60', 0, 1772885925),
('01465ac90eba11f053f30fd9eaf82ca7', 0, 1772886857),
('02550870c75a623a59c85c0b7a37ff43', 0, 1772561260),
('02bea3f260170ae89986fa9b6ac89eae', 0, 1772561177),
('0334c0087451adee9dddce6cdbbdc3a0', 0, 1772896448),
('039cb3d619ae1a127dd2d34737fc814d', 0, 1772475484),
('043b00ade4923bb9a6d98696f273dfc5', 0, 1772475484),
('057b662fd9988b0474f260139b8cc450', 0, 1772918338),
('072d5f6636ececb90a5e82774ff5a1ef', 0, 1772479020),
('077a0e1a291e169ac73d9af298ed1e4b', 0, 1772561177),
('07a744ecc1b1e01da833a03b914dcaf1', 0, 1772890292),
('07c0d18c07e172ea4dfbb4a272e60399', 0, 1772918332),
('07f44d56464b0667aab32c4b319c41bf', 0, 1774034271),
('08469226fb931e8ef2949b575735cf86', 0, 1772561275),
('092382a496b4671284b243f95fee5727', 0, 1772885936),
('09e5a0df603d86b23cfea9fc7e08ebb2', 0, 1772483312),
('0cfd528530a82e2056bb22c6b395159e', 0, 1774182115),
('0d2b62d26fb0b74b3cb46c4a646f00d4', 0, 1772918690),
('0dcb05fc3d2e311eae43ffd39227906b', 0, 1772475487),
('0e6864a88058b7b71bec7e176fa18263', 0, 1772475495),
('0f42d611ef7fc0839f40b1ab503b272a', 0, 1772475479),
('0fb1e0e3deddec2121b263cd1f5e114c', 0, 1772896996),
('0fd8a5b895cbe6e325008c6b808afa80', 0, 1772881119),
('0fe4afc42634f0593a160b307024bbad', 0, 1772891798),
('107cba153ae18b434b94e70cad3d9ed0', 0, 1774183784),
('1108176150fffa485904d345203250ee', 0, 1772888229),
('117adbb69cfc8690f13b67b8a30dbb0c', 0, 1772657737),
('12a3e6d2a4c07f54699f294acc6cbc49', 0, 1772478943),
('1390af3b6845e1c189e4bad9fb464822', 0, 1773957069),
('14605ce591d6684da69283249d603fa3', 0, 1774181307),
('14a713a5c9f780f11a6d6c22e8a1eda7', 0, 1774034251),
('154e0545e2d56bae852259b4186b4de0', 0, 1772918629),
('158c3c43435bbf66367e0863717d9bd3', 0, 1772920027),
('165a7772d824ab0780aec74f97583ac4', 0, 1772658220),
('1683c2df2a01db95e4228ef15bdb5762', 0, 1772919781),
('16b4230c95a551683cd70f1aa44b9a9e', 0, 1772893812),
('18a3e200900f69b0e5944710cac4146c', 0, 1772553511),
('1980f0fdc94699eb64c00d3bc4bd8b36', 0, 1773861006),
('1ab95815e3e382029ed58e851ab51ee8', 0, 1772876615),
('1b1611c3928b74bac419a4f92be67339', 0, 1772918769),
('1d8b88bcb3dbf66774d255a5da60414a', 0, 1773601291),
('1dad0a67215eb0b2f190d79f00cd47d1', 0, 1772918253),
('1e09a9eaf538ccac07603bec3d2d48ce', 0, 1772475489),
('1f0d7fc1ed68e7abb1076b351b7be7d3', 0, 1773347780),
('1f5447aae600bb4d2198f3d36299ce8a', 0, 1773861012),
('1f585264a1d177f174117ad82f5444cb', 0, 1772894664),
('1f9cf89c36c236122a41b68ffc219310', 0, 1774035866),
('20e2fd99617c3a23f1aee12bb8af388d', 0, 1772919811),
('2129fe0eda421df6622ea2096ee0de4d', 0, 1772561260),
('218e477882c693f0ab9fa582298b8f63', 0, 1772479019),
('21e3760e6586f7e893208849fec1cc51', 0, 1772884466),
('21e7155e0e3eecdf73d72d8afb7af7bf', 0, 1772475477),
('21e8402c0efd258dee01242101f3e3df', 0, 1773861661),
('220d87017748e70ada8f378fc49824e2', 0, 1772881257),
('232851522e859d6c0048a320e52391c4', 0, 1772974957),
('2329d2e8d47297d2b843b0f3be296ed2', 0, 1772888214),
('2449208e017a4d0e429c7ecaf7f865d0', 0, 1773251027),
('252139818166386078377ca07f7dc105', 0, 1772561177),
('265cc0b3325e94f0718a0e174b68e1ee', 0, 1772477553),
('2682e577ac78c542654f2691e517a63d', 0, 1772895700),
('26f344501bfa330f4ced510a9071ef88', 0, 1773585247),
('271933b640d5324040bc454981d6f8ba', 0, 1773685813),
('272dc28aad59bf69039be5d9a9ab1cf4', 0, 1772478323),
('28429f228045117b5b1fff85af8940df', 0, 1774437987),
('28555842dcfcff5e526e103f73dcd810', 0, 1772885934),
('28673bcc677373363a989e3d53fe42f3', 0, 1772896549),
('29b47ff2333b47bfe9187626b190f1fd', 0, 1772561177),
('2a1249083c328b47296e8caf2599dd6c', 0, 1772479098),
('2b1ad8a4559ac6ffb6560b48622e030c', 0, 1774180954),
('2b2e600fd6980e0b2a5a7608db0d1739', 0, 1774036538),
('2d08b9fffbf94a990de4832cab5ab6e7', 0, 1772561177),
('2d1301683189fdaf39867ca3cdf7f49d', 0, 1774093767),
('2dec28b90878ac2e607c97dbf619ebfe', 0, 1772561260),
('2e0e1acd2a75a340e0a9afbacf63af7d', 0, 1772561177),
('2eadd0667d54eaeb17c055f0fe245822', 0, 1772561177),
('300265b25480bdea0ecd7ec824954184', 0, 1772561177),
('31c34da72013eedd51341fa46a785c82', 0, 1772475484),
('31d03cbedf796d2c1b416531ad739fab', 0, 1772896422),
('31ea01f4eb9cbd1d96875ea638eebf52', 0, 1772561279),
('32071bf6e231837eef08933097f228a9', 0, 1772887132),
('32445f158d78b90b9cfc5c425e28d12a', 0, 1772918573),
('32d924893f4c1f247d696ae820b07ec0', 0, 1773599997),
('331427d69e53c5f53cbaeac7cab72139', 0, 1772888037),
('35e417a8ec3e1af02ac9e9d95e55c487', 0, 1772917712),
('3624329ba39e8052040d59678feb9c67', 0, 1772897489),
('3681992e18781f991958166c8af83c92', 0, 1773603547),
('378474a2952d06a0d9c1003da7c986c6', 0, 1772888040),
('385d4d015017278f447b8396eb4b1999', 0, 1772885923),
('38dbe041fa2b220ef61668534bdb64ca', 0, 1772561281),
('39dd7ae030e0ac3e7f9b8b18ba91a29e', 0, 1772475479),
('3afd100b9a0b8b93538a3924ab9d5cbe', 0, 1774181158),
('3b994235c5d9f564e9a343f747278ef6', 0, 1772896996),
('3c0e7d3e5a0f7870abdea32acc2a7679', 0, 1772971809),
('3e3f3e7e182956d94c60b9c9aca49619', 0, 1772880367),
('3fb3bed797495a5eebd9cbd59eebda25', 0, 1772885853),
('40233094b2e044d49b9bdd18e650789b', 0, 1772561260),
('40b2f3b3ab00a8084ed7726d40b5f16a', 0, 1772561177),
('41069b053dd9f20da22115bb544a9f7d', 0, 1772475485),
('412161df97f86cd327b9b5f2f8b22a12', 0, 1772475492),
('44aaa5073815b5b68ad914c6603dafba', 0, 1774093753),
('46424754904a7fa73cca790dba4037fc', 0, 1772475502),
('46ee5840df862b4077f139b564497501', 0, 1772475487),
('4705a3b524935ae17a152fb4300bc4f7', 0, 1772895252),
('497b4ea613c83658f20e258922521244', 0, 1772561177),
('4993016fd01097577b3150703c2cce77', 0, 1772477224),
('49d6f65d47f83528a745bdd308bf5fff', 0, 1772478935),
('4a8b0c9155a40c1e61e591a850783be7', 0, 1772561265),
('4b5348ba37ec890390f39171c2aabeb7', 0, 1772478898),
('4caee392f7a68dea43fd212e4a6bc10d', 0, 1772561177),
('4da08968714ac91c69bec5da25977639', 0, 1772918569),
('4ddc8c58296a76826b10dec33df9f57e', 0, 1772477553),
('4e05f830672049df35b32d6e9651c64e', 0, 1772553951),
('4e7f53dd0c08876eb6f7a09bb47f4be8', 0, 1773954982),
('4ec297f5243274457a9ce25db6513b96', 0, 1772483299),
('5074fdca4a45f8e941ef9d754ad6f35d', 0, 1774027836),
('51d8b6f2c854746ffa0321ff61902a2e', 0, 1773585164),
('5222ec69c9fb54507649d584c621ed44', 0, 1772561181),
('5276b51b21bf42ef369d3f87fce3fd43', 0, 1773861614),
('528477273aa62ec0da2c452305aaa541', 0, 1772880407),
('52f70d0bb3241555b0e3422dea47c9ad', 0, 1772657537),
('53cdc9afc3f768bb9211bc7f1ea98652', 0, 1772561177),
('53e04e3f18c63416939e13f874a2912f', 0, 1774034073),
('558ce1b792cf0d424c299e3aec5421c9', 0, 1772919718),
('559e1b78370026e47e0238a146829704', 0, 1773600018),
('55a008ec937d82d0bd984354ae5f3421', 0, 1772477425),
('579ffc43c57e34769ab5b2dee02214a5', 0, 1772888231),
('581d34c650a95452e96c614ce3116ef9', 0, 1772477400),
('58a66ef1dce98553d1c048718c32b9d2', 0, 1774449786),
('593000b718cc7eb0b9b937fc0d8a5cf7', 0, 1772467073),
('59ac4ca747691c2e3453f737af27d48f', 0, 1773600029),
('5aa16af4b6d6c04b4516a4b2ae2e96b0', 0, 1772918322),
('5ab1367bdfdb94231a3a4714cce9b8d4', 0, 1772893800),
('5b9341058beb539efc198b9aafa9cbf7', 0, 1772917328),
('5bdbfc4260a3cb81f3eaff837275ec5a', 0, 1772475484),
('5c79500352a4b939c9ee72b606414f5f', 0, 1774182154),
('5c94c0e0c3956f711ca3c54e7727a022', 0, 1772477172),
('5d5ced613c6e675d892e59128c201805', 0, 1772561277),
('5d62cff855f95e0162f3da279296b1b7', 0, 1772475487),
('5dca7e02f5f32177432ed76a14839db8', 0, 1773601274),
('5e33e9d053c07ee8de44733f3be770f0', 0, 1773488542),
('5e58a857274680df53b2eaa875cfe40b', 0, 1774174757),
('5f8316c15a6b24dc6603b2640cdc3230', 0, 1772483311),
('609c11321dbb897984890eb8b1f2aff8', 0, 1773860049),
('614a1b2d245cef67e855b73d0c9e19de', 0, 1772477609),
('61bd17f68dbce3b38eed7202af13ca9f', 0, 1774180213),
('629b07be78dfca987bb985b58b005ad1', 0, 1772660433),
('658149dcceb172a30c87837e9ae8331d', 0, 1772561177),
('65f0ff27a6c0899aeed1c814b1e77b5d', 0, 1772963924),
('677bb6132a7e7479582bb620fb7ceb1a', 0, 1772881263),
('67b49a31ed0c9b07d6e6aee4eff96362', 0, 1772881260),
('688ba0afeceaf81a04d5639da5fc7766', 0, 1772561177),
('6b18fef07518cff33fdc0799f5e2b563', 0, 1772918605),
('6c0e760f40a200fe81d70759b0efdc30', 0, 1772561177),
('6c42cd9133e363ad6301fe36c797205b', 0, 1772561177),
('6c684e5f3fb6236c61409461eac7029d', 0, 1773600023),
('6d046c236dbee7b5be53c6219d65d5df', 0, 1772475230),
('6d4c5601eb26917ddd092b515daf089b', 0, 1774181005),
('6e2278a87f5ce15708b4537751c3ccba', 0, 1772477767),
('6e41f59c58659a49a917d183ca6e809b', 0, 1772963304),
('6faa6f9289cc532c57b9d16170e5ac44', 0, 1772553947),
('709d5b1be2abbabe2aa184fb327ce05d', 0, 1772467068),
('712be2937bf5585c72df548bc271d298', 0, 1773488540),
('73636bcb23ab861b5fe8f6c889a1dc86', 0, 1773587386),
('7366873552289ccc1c6b892f08c6cca4', 0, 1774438078),
('73bf99d589a02e95577f7fc1a85f43ee', 0, 1772561283),
('743778d013b0ca4aadd88baa5c931783', 0, 1772885848),
('757bc42380c0298b767fbdc01e605c8d', 0, 1773347777),
('75d3ce583f84980b097b7f638446856e', 0, 1772475477),
('7605c391163f06aa6e5d1d4a3d93611a', 0, 1774181204),
('788966e7a0cc74d83a9096350f82bc51', 0, 1772475484),
('790835017e06d3c176865d8b8ad4aa44', 0, 1773948481),
('7917c3732b4bf2b77d34f45aef1c23ed', 0, 1772881246),
('7a26154252ec7f1e61a90708ca31bdd7', 0, 1772658223),
('7a58afb877d169f50bc0978a389b8107', 0, 1774180564),
('7b2929ab7b687068d0fc99629f9941b7', 0, 1773948517),
('7b32914ad590baa28795e5180c3ad1e2', 0, 1773861037),
('7c38c0488c116e174667eaa372ce02ec', 0, 1772475477),
('7c736808ae00798fbd73bf9a635f3429', 0, 1772561177),
('7c7ca50299164f784a028cc570780d40', 0, 1774181146),
('7eb0e6f6e25fe9528639f00a30caad58', 0, 1772475479),
('7f414276dbee6acffccea9fdd8d499db', 0, 1772888218),
('80dfbe0c06d38b8fb203863e16e11860', 0, 1772919721),
('811b7ffec23245460c2844a11582134b', 0, 1774027934),
('81a4a98cb2d9b6624545d22341b8abf9', 0, 1772997477),
('823ad41f0fcbd346fecb5452124a5611', 0, 1772972122),
('823f31808d0afbc48139517985a8eca3', 0, 1772972129),
('83c6a84717cab66812e44e4f71af46c0', 0, 1773955013),
('840996c1276cd621e62a7dfbf2af1062', 0, 1772477551),
('8433c446bae8f2f8854cc122d8097cab', 0, 1772561286),
('848c076d9e785e05ab928d1629900660', 0, 1772740303),
('84b92c1061321b466637677ac6941bb8', 0, 1773600010),
('84bb857fb719d302ae358ab3615aacb7', 0, 1772972125),
('8568d6d978f4ea7e6487bba45443d93f', 0, 1773860363),
('856f6e0c8d9149dfa275a71623bbd6de', 0, 1772478324),
('85722f6e30e017d97e5e561970512cd6', 0, 1772477177),
('88a9fdd9c848da228145c49379109806', 0, 1772485004),
('8a5b6b6e6ae2794a8430c65acad61a59', 0, 1774181011),
('8b2900551d15ecc00b7121bb03a650ec', 0, 1773954936),
('8bcaca8180109554f3a82235148b84c3', 0, 1773603562),
('8c0c4d7ae890527851db59502d61af1c', 0, 1772475482),
('8c5117609530a180b346d810bf03642c', 0, 1774034430),
('8cb5525d5c540432f767ee5ddc399c5f', 0, 1772972102),
('8ce30617eb68f6f7329c42b0d8ea14bc', 0, 1772971821),
('8d495b55db76ebc7985cf683e97d3829', 0, 1772919789),
('8e3cc57d5cfbbf996c4dc768a987b4d9', 0, 1773861651),
('8e55daf53c0c109c3bb003d3420beaa6', 0, 1773860078),
('8ea62b6b332c771f9d1f4af42688d361', 0, 1773347771),
('8ebd608f50aad229b1d8ad5f7e269e57', 0, 1773860440),
('90517af1a18443099dca665f4eee7043', 0, 1772561170),
('9055de710e2a9a4dbfbadc6ac3396c41', 0, 1772876610),
('9202e6178ef0e4ec54d277f634b87d65', 0, 1774183763),
('9253cc93bac413e659fc54c28259d7ea', 0, 1772561177),
('925c6b84da1ee154564268a9634bcf36', 0, 1772963957),
('9368b714164a2183ac5226946c8ad40d', 0, 1773954943),
('94a93191bfde77f0b182f4b79f7859bc', 0, 1772992898),
('9695fc6bd75bf6b840f4e268151c639f', 0, 1772561189),
('9720677ac8c6e911f3662d8371080186', 0, 1772657867),
('97385fad1b4cdfac42c3f7e9122cd6b1', 0, 1772561177),
('978ea158e0b028558a6c84a43533d7d3', 0, 1773860981),
('9854c9e69de1621142fba4d905f665c2', 0, 1772475484),
('9877beedb32e899e7eb4f7bc43d709d9', 0, 1772971843),
('9ab6ab356b95846ae992850d5b1329c4', 0, 1774438113),
('9c7ac8b4ae70afe2d0885a73a8b67925', 0, 1772917325),
('9d4ecd10de1b7e70e813a939a4817af3', 0, 1772475477),
('9ef1772046ec08726bee79e8a828a68e', 0, 1774034089),
('9f509fba31be2e02351b775a282ac42d', 0, 1772477552),
('9fcb7b95d8cd9433d928ba1397fd6247', 0, 1772888198),
('a329c2d4d8ac80590217af1fe696417c', 0, 1772561177),
('a368cf9218480c6d25c4db4184e47fb1', 0, 1772561175),
('a407f3346b31cce0280af801f7ee3284', 0, 1774093702),
('a425e7dbb252f150fcee6dc6ed3b3bd5', 0, 1772477174),
('a4fd76929377ebaa836e42bb027a816a', 0, 1772890139),
('a57ade693398e49bd3dd7738ddf5779b', 0, 1774092739),
('a5c5392ff454bee1875995513bca1dfb', 0, 1774036525),
('a7f592562d1a221bb32e46c442fbdd52', 0, 1773860966),
('ab25a406d63c4f52c04aa3f9fa78b55b', 0, 1772888246),
('ab547d3af93b75e8ebd7d5db39abf0c9', 0, 1772972133),
('ab99044d11848595c6b41f50ba174b5c', 0, 1773860019),
('abe4f2bff50bd9c7d1b2c2ecce19820f', 0, 1772561177),
('accaf9c8e4be8f849ea34ba8351a7a3b', 0, 1774438082),
('ade4c53a41d185e47bff710eb0150ccd', 0, 1774180297),
('af4b5c839e1bed082913fe47c46592b6', 0, 1774181409),
('af5d55a6657172267f3d4a4836dd21cd', 0, 1772654594),
('af9b1ed1c2f311d8dbbd85d5d943566c', 0, 1772972105),
('afd88f2b70ce29b6df636bd6b4708d5a', 0, 1774028431),
('b0d7a53cec512afad49e395230488a82', 0, 1772561177),
('b1319840c2272798c74dceee71a5a0f8', 0, 1772561260),
('b1e6615bd1e5b3344666f8101290db5e', 0, 1772880359),
('b531e1bb47225a11951c70813da645eb', 0, 1772886852),
('b5406aab538c81b5467ca651b12daf4f', 0, 1772561173),
('b54e2e0db349bf12770f4f32200bcc97', 0, 1772992552),
('b5a11b9c0387225c09a9a7f2e3e23c7c', 0, 1772880343),
('b5f7c4a73d4c90016b1568469985ffc1', 0, 1774180251),
('b6891fa288eae3cb5b9b2eccd8f9f21c', 0, 1772881240),
('b765c8b0533e2ce8d423910b62c5dc91', 0, 1772483302),
('b78191c1e39ad522b58fa57bf151d3ab', 0, 1772479203),
('b813cafa5b5e0a8eca4cb3e7f0a49fd2', 0, 1772891191),
('b87d2bfb5941d86c54152d4707197cf7', 0, 1772477765),
('bf8171a3bb2cba4403670fdcc81d7dd4', 0, 1772561177),
('c18dced3634f34b9d271ac606c68a172', 0, 1774038832),
('c1c30839dfb53ec33e6205fd473af05a', 0, 1774042405),
('c2652d94017bd3d2e33d9c1c42385db7', 0, 1772918554),
('c3597ee0a54a27aadead2b984b6bbf6c', 0, 1773251025),
('c37cb24037d5f44ff9c98e60193aab07', 0, 1772893779),
('c535d6edad362577d3a3138a911f80af', 0, 1774038849),
('c536e6bb122e174fb2a3ded2e7f16231', 0, 1772561260),
('c55d3430feb7cc2fc54dc8093d62ad34', 0, 1772897392),
('c591d1af6fc3d32bae15ad8f90363168', 0, 1772888202),
('c655d73e73f165a5e53479c0554a31eb', 0, 1772561177),
('c7b1876aa197a2b4cb5672774d78898f', 0, 1772475487),
('c86c2a956798f4f2c0e5747bde60b651', 0, 1772479199),
('c87ffa8c78b68be6af1ace3813a564c7', 0, 1773599989),
('c8fa4af09e50d1e1a62daab805bfa447', 0, 1772561177),
('c9f02a22272dad2f872f2ed59a780211', 0, 1772561177),
('ca1d953a93cabeffce815f27304c96db', 0, 1772919825),
('ccd1e747b2324eca9ccfe506c4f27e3a', 0, 1772974955),
('cce79913e18d522216fc3a6c874b574d', 0, 1772963283),
('cf4859112f0394e4aef1a9962994602c', 0, 1773600002),
('d018d3ae131849cdf0c7227c4af47c80', 0, 1772972110),
('d18f6d580f36a341df1b7d5afd2292af', 0, 1772475487),
('d1c256d02f54152be9f06564556dde64', 0, 1772476664),
('d2dd86cf2081c898ca6af2befb79c092', 0, 1772477613),
('d396884a15b983889f4dba2fe8496782', 0, 1772467071),
('d421c37bd749be1843498a1e021f3e76', 0, 1772475477),
('d5af1edd92d8782f5c0efd6deecdf862', 0, 1774182128),
('d715059a4e6cee111eb63f89f7889389', 0, 1772887137),
('d87298516f46dc2ccfe3ae5de6759ea0', 0, 1772478327),
('d986f2955307c1efeafd45f7fa8a0ab4', 0, 1772477248),
('dc030b6a79ee8b84de12c53217266693', 0, 1772561177),
('dc0f9646c320fd2e665dd2e9cc5b0356', 0, 1772884461),
('dcffecc381d34367f34222c0c831e3be', 0, 1773587851),
('dea6c3e4f00f4778df4d821489f4d2fb', 0, 1772561260),
('df0db8ed1b07a7f960b73c415d4867c6', 0, 1772477611),
('df4fe1e502ff6ed398b0551b822d74bc', 0, 1772475477),
('e0bc30504d3fc5444fd3a194111c9c6a', 0, 1772479202),
('e0ccafad9c0ca0ce883ec3c088b8bf0c', 0, 1774034015),
('e28f23212203e7e067b18f503d81f04d', 0, 1774180506),
('e3d14b1dae53da07b5b74b004c32a6e3', 0, 1772896159),
('e522b1fa037b9840bb5da0ab530ae232', 0, 1772475479),
('e58ad86f1b8d687357c88a1cfe73a759', 0, 1772971817),
('e732101be042ab749a21712bd6b7a5a8', 0, 1772893869),
('e8315be6277e81a29086c150becfafb8', 0, 1772885968),
('e837b53a09551b034c7651a593becacb', 0, 1772561177),
('e8739ba3a874359bcf85c292facbe55a', 0, 1773585360),
('e98a8fa490bc7852d0d57d6c8206c74a', 0, 1772657742),
('e9e0ccd385f3cd6fe31903e663f286e5', 0, 1772893733),
('ea53b3a568a90a675708c9d5f944ed3c', 0, 1772891855),
('eaddaf39576a5622f4a557100bc8e599', 0, 1772997477),
('ec562d30e0b3015d05307e27ca6cbc1d', 0, 1772561177),
('ecfd07ea8dbed6927447c2de33af03f4', 0, 1773860045),
('edbfa41c5bdb73844b850dc28f2230a9', 0, 1772479269),
('eedeee6bbda3ef22ac5f3ae1620e73f8', 0, 1772561257),
('f0a42bf657ed16e4766284c1f784664f', 0, 1774180473),
('f2880283ae11b97957932685fe408621', 0, 1772477746),
('f37e715b028b0b9fe25d67ef76ac69ca', 0, 1772475479),
('f3b91bdf2bb7bcac830680c7d93d5708', 0, 1774438230),
('f3f16fc6f7edbb3dc3d76fcc5f4d81d2', 0, 1774038974),
('f47daee8a6bc7715963dab4540b928d1', 0, 1772475487),
('f4b9f9516dab95ca21d4317239043d01', 0, 1773859987),
('f60c5ff7eee22da84dea89746c16a17c', 0, 1772897334),
('f6cf1d14b5d5b4377cff4e20bc3ac173', 0, 1772896994),
('f7447fbc4a0f960d67a879e674a3ef48', 0, 1772897340),
('f7bba1a176b7ae2880486afc402401c4', 0, 1772880344),
('f7d933e997042f1bddc983d59e7ffae8', 0, 1774180228),
('f7d9d4d5d2e36c530366299c98dfcf11', 0, 1772561177),
('f911339c6027c2b65f187878d821447f', 0, 1774035874),
('f9777bb881c4bac0550eb81ad1fe9cfb', 0, 1773954960),
('fa005d971d02a1d891a438e0f329aaec', 0, 1773957152),
('fa356de735520e3d3413bdd159c00cf7', 0, 1772561177),
('fb715db59a069bb3ebfcb2ec72798a5a', 0, 1772963287),
('fe4186d26b2b0be03317839b820433f6', 0, 1772475479),
('fecf9f119c6016fbc268c0e59f1e89af', 0, 1774438149),
('ff9573bfa14e75fe8ee7a2cea1ff1af1', 0, 1772561177),
('ffa45290a660938dd7ddcdd2924f9a96', 0, 1772561177);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `comments`
--

CREATE TABLE `comments` (
  `commentID` int(11) NOT NULL,
  `plugin` varchar(50) NOT NULL,
  `itemID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `comment` text NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp(),
  `parentID` int(11) DEFAULT 0,
  `modulname` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `contact`
--

CREATE TABLE `contact` (
  `contactID` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(200) NOT NULL,
  `sort` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `contact`
--

INSERT INTO `contact` (`contactID`, `name`, `email`, `sort`) VALUES
(1, 'Administrator', 'info@nexpell.de', 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `email`
--

CREATE TABLE `email` (
  `emailID` int(1) NOT NULL,
  `user` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `host` varchar(255) NOT NULL,
  `port` int(5) NOT NULL,
  `debug` int(1) NOT NULL,
  `auth` int(1) NOT NULL,
  `html` int(1) NOT NULL,
  `smtp` int(1) NOT NULL,
  `secure` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `email`
--

INSERT INTO `email` (`emailID`, `user`, `password`, `host`, `port`, `debug`, `auth`, `html`, `smtp`, `secure`) VALUES
(1, '', '', '', 25, 0, 0, 1, 0, 0);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `failed_login_attempts`
--

CREATE TABLE `failed_login_attempts` (
  `id` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `ip` varchar(45) NOT NULL,
  `attempt_time` datetime NOT NULL DEFAULT current_timestamp(),
  `status` enum('failed','blocked') DEFAULT 'failed',
  `reason` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `failed_login_attempts`
--

INSERT INTO `failed_login_attempts` (`id`, `userID`, `ip`, `attempt_time`, `status`, `reason`, `email`) VALUES
(1, 2, '94.31.75.87', '2026-03-08 17:29:48', 'failed', 'Login fehlgeschlagen', 't-seven@webspell-rm.de'),
(2, 2, '94.31.75.87', '2026-03-08 17:30:27', 'failed', 'Login fehlgeschlagen', 't-seven@webspell-rm.de'),
(3, 2, '94.31.75.87', '2026-03-08 17:30:38', 'failed', 'Login fehlgeschlagen', 't-seven@webspell-rm.de');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `link_clicks`
--

CREATE TABLE `link_clicks` (
  `id` int(11) NOT NULL,
  `plugin` varchar(50) DEFAULT NULL,
  `itemID` int(11) DEFAULT NULL,
  `url` text DEFAULT NULL,
  `clicked_at` datetime DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `referrer` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `link_clicks`
--

INSERT INTO `link_clicks` (`id`, `plugin`, `itemID`, `url`, `clicked_at`, `ip_address`, `user_agent`, `referrer`) VALUES
(1, 'partners', 1, 'https://www.nexpell.de', '2026-03-05 20:39:46', '94.31.75.87', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0', 'https://www.test.nexpell.de/index.php?site=partners'),
(2, 'links', 5, 'https://all-inkl.com', '2026-03-12 19:58:36', '94.31.75.87', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0', NULL),
(3, 'sponsors', 1, 'https://www.nexpell.de', '2026-03-19 01:25:32', '45.148.10.62', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36', ''),
(4, 'sponsors', 4, 'https://www.nexpell.de', '2026-03-19 01:25:32', '45.148.10.62', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36', ''),
(5, 'sponsors', 3, 'https://www.nexpell.de', '2026-03-19 01:25:32', '45.148.10.62', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36', ''),
(6, 'sponsors', 2, 'https://www.nexpell.de', '2026-03-19 01:25:32', '45.148.10.62', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36', ''),
(7, 'sponsors', 6, 'https://www.nexpell.de', '2026-03-19 01:25:32', '45.148.10.62', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36', ''),
(8, 'sponsors', 5, 'https://www.nexpell.de', '2026-03-19 01:25:32', '45.148.10.62', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36', ''),
(9, 'sponsors', 1, 'https://www.nexpell.de', '2026-03-19 01:25:39', '45.148.10.62', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36', ''),
(10, 'sponsors', 2, 'https://www.nexpell.de', '2026-03-19 01:25:40', '45.148.10.62', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36', ''),
(11, 'sponsors', 4, 'https://www.nexpell.de', '2026-03-19 01:25:40', '45.148.10.62', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36', ''),
(12, 'sponsors', 3, 'https://www.nexpell.de', '2026-03-19 01:25:40', '45.148.10.62', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36', ''),
(13, 'sponsors', 5, 'https://www.nexpell.de', '2026-03-19 01:25:40', '45.148.10.62', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36', ''),
(14, 'sponsors', 6, 'https://www.nexpell.de', '2026-03-19 01:25:40', '45.148.10.62', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36', '');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `navigation_dashboard_categories`
--

CREATE TABLE `navigation_dashboard_categories` (
  `catID` int(11) NOT NULL,
  `modulname` varchar(255) NOT NULL,
  `fa_name` varchar(255) NOT NULL DEFAULT '',
  `sort_art` int(11) DEFAULT 0,
  `sort` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `navigation_dashboard_categories`
--

INSERT INTO `navigation_dashboard_categories` (`catID`, `modulname`, `fa_name`, `sort_art`, `sort`) VALUES
(1, 'cat_system', 'bi bi-gear', 0, 1),
(2, 'cat_statistics', 'bi bi-bar-chart-line', 0, 2),
(3, 'cat_users', 'bi bi-person', 0, 3),
(4, 'cat_security', 'bi bi-shield-lock', 0, 4),
(5, 'cat_team', 'bi bi-people', 0, 5),
(6, 'cat_design', 'bi bi-layout-text-window-reverse', 0, 6),
(7, 'cat_plugins', 'bi bi-puzzle', 0, 7),
(8, 'cat_content', 'bi bi-card-checklist', 0, 8),
(9, 'cat_media', 'bi bi-image', 0, 9),
(10, 'cat_slider_header', 'bi bi-fast-forward-btn', 0, 10),
(11, 'cat_tools_game', 'bi bi-controller', 0, 11),
(12, 'cat_social', 'bi bi-steam', 0, 12),
(13, 'cat_partners', 'bi bi-link', 0, 13);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `navigation_dashboard_lang`
--

CREATE TABLE `navigation_dashboard_lang` (
  `id` int(11) NOT NULL,
  `content_key` varchar(191) NOT NULL,
  `language` varchar(8) NOT NULL DEFAULT 'de',
  `content` varchar(255) NOT NULL DEFAULT '',
  `modulname` varchar(255) NOT NULL DEFAULT '',
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `lang` varchar(10) NOT NULL DEFAULT 'de',
  `translation` text DEFAULT NULL,
  `name` varchar(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `navigation_dashboard_lang`
--

INSERT INTO `navigation_dashboard_lang` (`id`, `content_key`, `language`, `content`, `modulname`, `updated_at`, `lang`, `translation`, `name`) VALUES
(1, 'nav_cat_1', 'de', 'System & Einstellungen', 'cat_system', '2026-03-03 17:05:01', 'de', NULL, ''),
(2, 'nav_cat_1', 'en', 'System & Settings', 'cat_system', '2026-03-03 17:05:01', 'de', NULL, ''),
(3, 'nav_cat_1', 'it', 'Sistema e Impostazioni', 'cat_system', '2026-03-03 17:05:01', 'de', NULL, ''),
(4, 'nav_cat_2', 'de', 'Statistiken', 'cat_statistics', '2026-03-03 17:05:01', 'de', NULL, ''),
(5, 'nav_cat_2', 'en', 'Statistics', 'cat_statistics', '2026-03-03 17:05:01', 'de', NULL, ''),
(6, 'nav_cat_2', 'it', 'Statistiche', 'cat_statistics', '2026-03-03 17:05:01', 'de', NULL, ''),
(7, 'nav_cat_3', 'de', 'Benutzer & Rollen', 'cat_users', '2026-03-03 17:05:01', 'de', NULL, ''),
(8, 'nav_cat_3', 'en', 'Users & Roles', 'cat_users', '2026-03-03 17:05:01', 'de', NULL, ''),
(9, 'nav_cat_3', 'it', 'Utenti e Ruoli', 'cat_users', '2026-03-03 17:05:01', 'de', NULL, ''),
(10, 'nav_cat_4', 'de', 'Sicherheit', 'cat_security', '2026-03-03 17:05:01', 'de', NULL, ''),
(11, 'nav_cat_4', 'en', 'Security', 'cat_security', '2026-03-03 17:05:01', 'de', NULL, ''),
(12, 'nav_cat_4', 'it', 'Sicurezza', 'cat_security', '2026-03-03 17:05:01', 'de', NULL, ''),
(13, 'nav_cat_5', 'de', 'Teamverwaltung', 'cat_team', '2026-03-03 17:05:01', 'de', NULL, ''),
(14, 'nav_cat_5', 'en', 'Team Management', 'cat_team', '2026-03-03 17:05:01', 'de', NULL, ''),
(15, 'nav_cat_5', 'it', 'Gestione Team', 'cat_team', '2026-03-03 17:05:01', 'de', NULL, ''),
(16, 'nav_cat_6', 'de', 'Design & Layout', 'cat_design', '2026-03-03 17:05:01', 'de', NULL, ''),
(17, 'nav_cat_6', 'en', 'Design & Layout', 'cat_design', '2026-03-03 17:05:01', 'de', NULL, ''),
(18, 'nav_cat_6', 'it', 'Design e Layout', 'cat_design', '2026-03-03 17:05:01', 'de', NULL, ''),
(19, 'nav_cat_7', 'de', 'Plugins & Erweiterungen', 'cat_plugins', '2026-03-03 17:05:01', 'de', NULL, ''),
(20, 'nav_cat_7', 'en', 'Plugins & Extensions', 'cat_plugins', '2026-03-03 17:05:01', 'de', NULL, ''),
(21, 'nav_cat_7', 'it', 'Plugin ed Estensioni', 'cat_plugins', '2026-03-03 17:05:01', 'de', NULL, ''),
(22, 'nav_cat_8', 'de', 'Webinhalte', 'cat_content', '2026-03-03 17:05:01', 'de', NULL, ''),
(23, 'nav_cat_8', 'en', 'Website Content', 'cat_content', '2026-03-03 17:05:01', 'de', NULL, ''),
(24, 'nav_cat_8', 'it', 'Contenuti Web', 'cat_content', '2026-03-03 17:05:01', 'de', NULL, ''),
(25, 'nav_cat_9', 'de', 'Medien & Projekte', 'cat_media', '2026-03-03 17:05:01', 'de', NULL, ''),
(26, 'nav_cat_9', 'en', 'Media & Projects', 'cat_media', '2026-03-03 17:05:01', 'de', NULL, ''),
(27, 'nav_cat_9', 'it', 'Media e Progetti', 'cat_media', '2026-03-03 17:05:01', 'de', NULL, ''),
(28, 'nav_cat_10', 'de', 'Header & Slider', 'cat_slider_header', '2026-03-03 17:05:01', 'de', NULL, ''),
(29, 'nav_cat_10', 'en', 'Header & Slider', 'cat_slider_header', '2026-03-03 17:05:01', 'de', NULL, ''),
(30, 'nav_cat_10', 'it', 'Header e Slider', 'cat_slider_header', '2026-03-03 17:05:01', 'de', NULL, ''),
(31, 'nav_cat_11', 'de', 'Game & Voice Tools', 'cat_tools_game', '2026-03-03 17:05:01', 'de', NULL, ''),
(32, 'nav_cat_11', 'en', 'Game & Voice Tools', 'cat_tools_game', '2026-03-03 17:05:01', 'de', NULL, ''),
(33, 'nav_cat_11', 'it', 'Game e Voice Tools', 'cat_tools_game', '2026-03-03 17:05:01', 'de', NULL, ''),
(34, 'nav_cat_12', 'de', 'Social Media', 'cat_social', '2026-03-03 17:05:01', 'de', NULL, ''),
(35, 'nav_cat_12', 'en', 'Social Media', 'cat_social', '2026-03-03 17:05:01', 'de', NULL, ''),
(36, 'nav_cat_12', 'it', 'Social Media', 'cat_social', '2026-03-03 17:05:01', 'de', NULL, ''),
(37, 'nav_cat_13', 'de', 'Downloads & Partner', 'cat_partners', '2026-03-03 17:05:01', 'de', NULL, ''),
(38, 'nav_cat_13', 'en', 'Downloads & Partners', 'cat_partners', '2026-03-03 17:05:01', 'de', NULL, ''),
(39, 'nav_cat_13', 'it', 'Download e Sponsor', 'cat_partners', '2026-03-03 17:05:01', 'de', NULL, ''),
(40, 'nav_link_1', 'de', 'Webserver-Info', 'ac_overview', '2026-03-03 17:05:01', 'de', NULL, ''),
(41, 'nav_link_1', 'en', 'Webserver Info', 'ac_overview', '2026-03-03 17:05:01', 'de', NULL, ''),
(42, 'nav_link_1', 'it', 'Informazioni Sul Sito', 'ac_overview', '2026-03-03 17:05:01', 'de', NULL, ''),
(43, 'nav_link_2', 'de', 'Allgemeine Einstellungen', 'ac_settings', '2026-03-03 17:05:01', 'de', NULL, ''),
(44, 'nav_link_2', 'en', 'General Settings', 'ac_settings', '2026-03-03 17:05:01', 'de', NULL, ''),
(45, 'nav_link_2', 'it', 'Impostazioni Generali', 'ac_settings', '2026-03-03 17:05:01', 'de', NULL, ''),
(46, 'nav_link_3', 'de', 'Admincenter Navigation', 'ac_dashboard_navigation', '2026-03-03 17:05:01', 'de', NULL, ''),
(47, 'nav_link_3', 'en', 'Admincenter Navigation', 'ac_dashboard_navigation', '2026-03-03 17:05:01', 'de', NULL, ''),
(48, 'nav_link_3', 'it', 'Menu Navigazione Admin', 'ac_dashboard_navigation', '2026-03-03 17:05:01', 'de', NULL, ''),
(49, 'nav_link_4', 'de', 'E-Mail', 'ac_email', '2026-03-03 17:05:01', 'de', NULL, ''),
(50, 'nav_link_4', 'en', 'E-Mail', 'ac_email', '2026-03-03 17:05:01', 'de', NULL, ''),
(51, 'nav_link_4', 'it', 'E-Mail', 'ac_email', '2026-03-03 17:05:01', 'de', NULL, ''),
(52, 'nav_link_5', 'de', 'Kontakte', 'ac_contact', '2026-03-03 17:05:01', 'de', NULL, ''),
(53, 'nav_link_5', 'en', 'Contacts', 'ac_contact', '2026-03-03 17:05:01', 'de', NULL, ''),
(54, 'nav_link_5', 'it', 'Contatti', 'ac_contact', '2026-03-03 17:05:01', 'de', NULL, ''),
(55, 'nav_link_6', 'de', 'Datenbank', 'ac_database', '2026-03-03 17:05:01', 'de', NULL, ''),
(56, 'nav_link_6', 'en', 'Database', 'ac_database', '2026-03-03 17:05:01', 'de', NULL, ''),
(57, 'nav_link_6', 'it', 'Database', 'ac_database', '2026-03-03 17:05:01', 'de', NULL, ''),
(58, 'nav_link_7', 'de', 'Sprachen verwalten', 'ac_languages', '2026-03-03 17:05:01', 'de', NULL, ''),
(59, 'nav_link_7', 'en', 'Manage Languages', 'ac_languages', '2026-03-03 17:05:01', 'de', NULL, ''),
(60, 'nav_link_7', 'it', 'Gestisci lingue', 'ac_languages', '2026-03-03 17:05:01', 'de', NULL, ''),
(61, 'nav_link_8', 'de', 'Spracheditor', 'ac_editlang', '2026-03-03 17:05:01', 'de', NULL, ''),
(62, 'nav_link_8', 'en', 'Language Editor', 'ac_editlang', '2026-03-03 17:05:01', 'de', NULL, ''),
(63, 'nav_link_8', 'it', 'Editor di Linguaggi', 'ac_editlang', '2026-03-03 17:05:01', 'de', NULL, ''),
(64, 'nav_link_9', 'de', 'SEO-Metadaten', 'ac_seo_meta', '2026-03-03 17:05:01', 'de', NULL, ''),
(65, 'nav_link_9', 'en', 'SEO Metadata', 'ac_seo_meta', '2026-03-03 17:05:01', 'de', NULL, ''),
(66, 'nav_link_9', 'it', 'Metadati SEO', 'ac_seo_meta', '2026-03-03 17:05:01', 'de', NULL, ''),
(67, 'nav_link_10', 'de', 'Core aktualisieren', 'ac_update_core', '2026-03-03 17:05:01', 'de', NULL, ''),
(68, 'nav_link_10', 'en', 'Update Core', 'ac_update_core', '2026-03-03 17:05:01', 'de', NULL, ''),
(69, 'nav_link_10', 'it', 'Aggiorna Core', 'ac_update_core', '2026-03-03 17:05:01', 'de', NULL, ''),
(70, 'nav_link_11', 'de', 'Seiten Statistiken', 'ac_statistic', '2026-03-03 17:05:01', 'de', NULL, ''),
(71, 'nav_link_11', 'en', 'Page Statistics', 'ac_statistic', '2026-03-03 17:05:01', 'de', NULL, ''),
(72, 'nav_link_11', 'it', 'Pagina delle Statistiche', 'ac_statistic', '2026-03-03 17:05:01', 'de', NULL, ''),
(73, 'nav_link_12', 'de', 'Besucher Statistiken', 'ac_visitor_statistic', '2026-03-03 17:05:01', 'de', NULL, ''),
(74, 'nav_link_12', 'en', 'Visitor Statistics', 'ac_visitor_statistic', '2026-03-03 17:05:01', 'de', NULL, ''),
(75, 'nav_link_12', 'it', 'Statistiche Visitatori', 'ac_visitor_statistic', '2026-03-03 17:05:01', 'de', NULL, ''),
(76, 'nav_link_13', 'de', 'Besucher / Seitenzugriffe', 'ac_db_stats', '2026-03-03 17:05:01', 'de', NULL, ''),
(77, 'nav_link_13', 'en', 'Visitors / Pageviews', 'ac_db_stats', '2026-03-03 17:05:01', 'de', NULL, ''),
(78, 'nav_link_13', 'it', 'Visitatori / Visualizzazioni di pagina', 'ac_db_stats', '2026-03-03 17:05:01', 'de', NULL, ''),
(79, 'nav_link_14', 'de', 'Benutzer und Rollen', 'ac_user_roles', '2026-03-15 14:11:36', 'de', NULL, ''),
(80, 'nav_link_14', 'en', 'Users and Roles', 'ac_user_roles', '2026-03-15 14:11:36', 'de', NULL, ''),
(81, 'nav_link_14', 'it', 'Utenti e ruoli', 'ac_user_roles', '2026-03-15 14:11:36', 'de', NULL, ''),
(82, 'nav_link_15', 'de', 'Admin Security', 'ac_security_overview', '2026-03-03 17:05:01', 'de', NULL, ''),
(83, 'nav_link_15', 'en', 'Admin Security', 'ac_security_overview', '2026-03-03 17:05:01', 'de', NULL, ''),
(84, 'nav_link_15', 'it', 'Sicurezza Admin', 'ac_security_overview', '2026-03-03 17:05:01', 'de', NULL, ''),
(85, 'nav_link_16', 'de', 'Zugriffsprotokoll', 'ac_log_viewer', '2026-03-03 17:05:01', 'de', NULL, ''),
(86, 'nav_link_16', 'en', 'Access Log Viewer', 'ac_log_viewer', '2026-03-03 17:05:01', 'de', NULL, ''),
(87, 'nav_link_16', 'it', 'Visualizzatore Log di Accesso', 'ac_log_viewer', '2026-03-03 17:05:01', 'de', NULL, ''),
(88, 'nav_link_17', 'de', 'Webseiten Navigation', 'ac_webside_navigation', '2026-03-03 17:05:01', 'de', NULL, ''),
(89, 'nav_link_17', 'en', 'Website Navigation', 'ac_webside_navigation', '2026-03-03 17:05:01', 'de', NULL, ''),
(90, 'nav_link_17', 'it', 'Menu Navigazione Web', 'ac_webside_navigation', '2026-03-03 17:05:01', 'de', NULL, ''),
(91, 'nav_link_18', 'de', 'Themes Installer', 'ac_theme_installer', '2026-03-03 17:05:01', 'de', NULL, ''),
(92, 'nav_link_18', 'en', 'Themes Installer', 'ac_theme_installer', '2026-03-03 17:05:01', 'de', NULL, ''),
(93, 'nav_link_18', 'it', 'Installazione Themes', 'ac_theme_installer', '2026-03-03 17:05:01', 'de', NULL, ''),
(94, 'nav_link_19', 'de', 'Themes', 'ac_theme', '2026-03-03 17:05:01', 'de', NULL, ''),
(95, 'nav_link_19', 'en', 'Themes', 'ac_theme', '2026-03-03 17:05:01', 'de', NULL, ''),
(96, 'nav_link_19', 'it', 'Temi', 'ac_theme', '2026-03-03 17:05:01', 'de', NULL, ''),
(97, 'nav_link_20', 'de', 'Stylesheet bearbeiten', 'ac_stylesheet', '2026-03-03 17:05:01', 'de', NULL, ''),
(98, 'nav_link_20', 'en', 'Edit stylesheet', 'ac_stylesheet', '2026-03-03 17:05:01', 'de', NULL, ''),
(99, 'nav_link_20', 'it', 'Modifica stylesheet', 'ac_stylesheet', '2026-03-03 17:05:01', 'de', NULL, ''),
(100, 'nav_link_21', 'de', 'Kopfzeilen-Stil', 'ac_headstyle', '2026-03-03 17:05:01', 'de', NULL, ''),
(101, 'nav_link_21', 'en', 'Head Style', 'ac_headstyle', '2026-03-03 17:05:01', 'de', NULL, ''),
(102, 'nav_link_21', 'it', 'Stile intestazione', 'ac_headstyle', '2026-03-03 17:05:01', 'de', NULL, ''),
(103, 'nav_link_22', 'de', 'Startseite', 'ac_startpage', '2026-03-03 17:05:01', 'de', NULL, ''),
(104, 'nav_link_22', 'en', 'Start Page', 'ac_startpage', '2026-03-03 17:05:01', 'de', NULL, ''),
(105, 'nav_link_22', 'it', 'Pagina Principale', 'ac_startpage', '2026-03-03 17:05:01', 'de', NULL, ''),
(106, 'nav_link_23', 'de', 'Statische Seiten', 'ac_static', '2026-03-03 17:05:01', 'de', NULL, ''),
(107, 'nav_link_23', 'en', 'Static Pages', 'ac_static', '2026-03-03 17:05:01', 'de', NULL, ''),
(108, 'nav_link_23', 'it', 'Pagine Statiche', 'ac_static', '2026-03-03 17:05:01', 'de', NULL, ''),
(109, 'nav_link_24', 'de', 'Impressum', 'ac_imprint', '2026-03-03 17:05:01', 'de', NULL, ''),
(110, 'nav_link_24', 'en', 'Imprint', 'ac_imprint', '2026-03-03 17:05:01', 'de', NULL, ''),
(111, 'nav_link_24', 'it', 'Impronta Editoriale', 'ac_imprint', '2026-03-03 17:05:01', 'de', NULL, ''),
(112, 'nav_link_25', 'de', 'Datenschutz-Bestimmungen', 'ac_privacy_policy', '2026-03-03 17:05:01', 'de', NULL, ''),
(113, 'nav_link_25', 'en', 'Privacy Policy', 'ac_privacy_policy', '2026-03-03 17:05:01', 'de', NULL, ''),
(114, 'nav_link_25', 'it', 'Informativa sulla Privacy', 'ac_privacy_policy', '2026-03-03 17:05:01', 'de', NULL, ''),
(115, 'nav_link_26', 'de', 'Plugin Manager', 'ac_plugin_manager', '2026-03-03 17:05:01', 'de', NULL, ''),
(116, 'nav_link_26', 'en', 'PluginManager', 'ac_plugin_manager', '2026-03-03 17:05:01', 'de', NULL, ''),
(117, 'nav_link_26', 'it', 'Gestore di Plugin', 'ac_plugin_manager', '2026-03-03 17:05:01', 'de', NULL, ''),
(118, 'nav_link_27', 'de', 'Widgets verwalten', 'ac_plugin_widgets_setting', '2026-03-03 17:05:01', 'de', NULL, ''),
(119, 'nav_link_27', 'en', 'Manage widgets', 'ac_plugin_widgets_setting', '2026-03-03 17:05:01', 'de', NULL, ''),
(120, 'nav_link_27', 'it', 'Gestire i widget', 'ac_plugin_widgets_setting', '2026-03-03 17:05:01', 'de', NULL, ''),
(121, 'nav_link_28', 'de', 'Plugin Installer', 'ac_plugin_installer', '2026-03-03 17:05:01', 'de', NULL, ''),
(122, 'nav_link_28', 'en', 'Plugin Installer', 'ac_plugin_installer', '2026-03-03 17:05:01', 'de', NULL, ''),
(123, 'nav_link_28', 'it', 'Installazione Plugin', 'ac_plugin_installer', '2026-03-03 17:05:01', 'de', NULL, ''),
(124, 'nav_link_29', 'de', 'Footer', 'footer', '2026-03-15 14:11:36', 'de', NULL, ''),
(125, 'nav_link_29', 'en', 'Footer', 'footer', '2026-03-15 14:11:36', 'de', NULL, ''),
(126, 'nav_link_29', 'it', 'Footer', 'footer', '2026-03-15 14:11:36', 'de', NULL, ''),
(127, 'nav_link_30', 'de', 'Theme Grundeinstellungen', 'navigation', '2026-03-03 17:05:01', 'de', NULL, ''),
(128, 'nav_link_30', 'en', 'Theme Global Settings', 'navigation', '2026-03-03 17:05:01', 'de', NULL, ''),
(129, 'nav_link_30', 'it', 'Impostazioni globali del tema', 'navigation', '2026-03-03 17:05:01', 'de', NULL, ''),
(130, 'nav_link_31', 'de', 'Nutzungsbedingungen', 'ac_terms_of_service', '2026-03-15 14:11:36', 'de', NULL, ''),
(131, 'nav_link_31', 'en', 'Terms and Conditions', 'ac_terms_of_service', '2026-03-15 14:11:36', 'de', NULL, ''),
(132, 'nav_link_31', 'it', 'Termini e condizioni', 'ac_terms_of_service', '2026-03-15 14:11:36', 'de', NULL, ''),
(278, 'nav_link_32', 'de', 'Regeln', '', '2026-03-02 19:46:05', 'de', NULL, ''),
(279, 'nav_link_32', 'en', 'Rules', '', '2026-03-02 19:46:05', 'de', NULL, ''),
(280, 'nav_link_32', 'it', 'Regole', '', '2026-03-02 19:46:05', 'de', NULL, ''),
(281, 'nav_link_33', 'de', 'Regeln', '', '2026-03-02 19:56:00', 'de', NULL, ''),
(282, 'nav_link_33', 'en', 'Rules', '', '2026-03-02 19:56:00', 'de', NULL, ''),
(283, 'nav_link_33', 'it', 'Regole', '', '2026-03-02 19:56:00', 'de', NULL, ''),
(423, 'nav_link_0', 'de', 'Regeln', '', '2026-03-03 20:43:00', 'de', NULL, ''),
(424, 'nav_link_0', 'en', 'Rules', '', '2026-03-03 20:43:00', 'de', NULL, ''),
(425, 'nav_link_0', 'it', 'Regole', '', '2026-03-03 20:43:00', 'de', NULL, ''),
(426, 'nav_link_35', 'de', 'Regeln', '', '2026-03-04 18:38:39', 'de', NULL, ''),
(427, 'nav_link_35', 'en', 'Rules', '', '2026-03-04 18:38:39', 'de', NULL, ''),
(428, 'nav_link_35', 'it', 'Regole', '', '2026-03-04 18:38:39', 'de', NULL, ''),
(429, 'nav_link_36', 'de', 'Über uns', '', '2026-03-04 20:11:44', 'de', NULL, ''),
(430, 'nav_link_36', 'en', 'About Us', '', '2026-03-04 20:11:44', 'de', NULL, ''),
(431, 'nav_link_36', 'it', 'Chi siamo', '', '2026-03-04 20:11:44', 'de', NULL, ''),
(432, 'nav_link_37', 'de', 'Errungenschaften', '', '2026-03-04 20:21:11', 'de', NULL, ''),
(433, 'nav_link_37', 'en', 'Achievements', '', '2026-03-04 20:21:11', 'de', NULL, ''),
(434, 'nav_link_37', 'it', 'Risultati', '', '2026-03-04 20:21:11', 'de', NULL, ''),
(435, 'nav_link_38', 'de', 'Ueber uns', 'about', '2026-03-15 15:01:32', 'de', NULL, ''),
(436, 'nav_link_38', 'en', 'About Us', 'about', '2026-03-15 15:01:32', 'de', NULL, ''),
(437, 'nav_link_38', 'it', 'Chi siamo', 'about', '2026-03-15 15:01:32', 'de', NULL, ''),
(438, 'nav_link_39', 'de', 'Carousel', 'carousel', '2026-03-15 15:01:39', 'de', NULL, ''),
(439, 'nav_link_39', 'en', 'Carousel', 'carousel', '2026-03-15 15:01:39', 'de', NULL, ''),
(440, 'nav_link_39', 'it', 'Carosello Immagini', 'carousel', '2026-03-15 15:01:39', 'de', NULL, ''),
(441, 'nav_link_40', 'de', 'Userliste-Einstellungen', '', '2026-03-04 21:51:51', 'de', NULL, ''),
(442, 'nav_link_40', 'en', 'User List Settings', '', '2026-03-04 21:51:51', 'de', NULL, ''),
(443, 'nav_link_40', 'it', 'Impostazioni elenco utenti', '', '2026-03-04 21:51:51', 'de', NULL, ''),
(444, 'nav_link_41', 'de', 'Userliste-Einstellungen', '', '2026-03-04 21:57:44', 'de', NULL, ''),
(445, 'nav_link_41', 'en', 'User List Settings', '', '2026-03-04 21:57:44', 'de', NULL, ''),
(446, 'nav_link_41', 'it', 'Impostazioni elenco utenti', '', '2026-03-04 21:57:44', 'de', NULL, ''),
(447, 'nav_link_42', 'de', 'Userliste-Einstellungen', 'userlist', '2026-03-15 15:08:30', 'de', NULL, ''),
(448, 'nav_link_42', 'en', 'User List Settings', 'userlist', '2026-03-15 15:08:30', 'de', NULL, ''),
(449, 'nav_link_42', 'it', 'Impostazioni elenco utenti', 'userlist', '2026-03-15 15:08:30', 'de', NULL, ''),
(450, 'nav_link_43', 'de', 'Partner', 'partners', '2026-03-15 15:05:33', 'de', NULL, ''),
(451, 'nav_link_43', 'en', 'Partners', 'partners', '2026-03-15 15:05:33', 'de', NULL, ''),
(452, 'nav_link_43', 'it', 'Partner', 'partners', '2026-03-15 15:05:33', 'de', NULL, ''),
(453, 'nav_link_44', 'de', 'Artikel', '', '2026-03-05 20:51:40', 'de', NULL, ''),
(454, 'nav_link_44', 'en', 'Articles', '', '2026-03-05 20:51:40', 'de', NULL, ''),
(455, 'nav_link_44', 'it', 'Articoli', '', '2026-03-05 20:51:40', 'de', NULL, ''),
(456, 'nav_link_45', 'de', 'Discord', '', '2026-03-05 20:58:55', 'de', NULL, ''),
(457, 'nav_link_45', 'en', 'Discord', '', '2026-03-05 20:58:55', 'de', NULL, ''),
(458, 'nav_link_45', 'it', 'Discord', '', '2026-03-05 20:58:55', 'de', NULL, ''),
(459, 'nav_link_46', 'de', 'Download', 'downloads', '2026-03-15 15:01:48', 'de', NULL, ''),
(460, 'nav_link_46', 'en', 'Download', 'downloads', '2026-03-15 15:01:48', 'de', NULL, ''),
(461, 'nav_link_46', 'it', 'Download', 'downloads', '2026-03-15 15:01:48', 'de', NULL, ''),
(462, 'nav_link_47', 'de', 'Twitch', '', '2026-03-06 20:56:42', 'de', NULL, ''),
(463, 'nav_link_47', 'en', 'Twitch', '', '2026-03-06 20:56:42', 'de', NULL, ''),
(464, 'nav_link_47', 'it', 'Twitch', '', '2026-03-06 20:56:42', 'de', NULL, ''),
(465, 'nav_link_48', 'de', 'TeamSpeak', '', '2026-03-07 00:00:48', 'de', NULL, ''),
(466, 'nav_link_48', 'en', 'TeamSpeak', '', '2026-03-07 00:00:48', 'de', NULL, ''),
(467, 'nav_link_48', 'it', 'TeamSpeak', '', '2026-03-07 00:00:48', 'de', NULL, ''),
(468, 'nav_link_49', 'de', 'Artikel', '', '2026-03-07 13:18:38', 'de', NULL, ''),
(469, 'nav_link_49', 'en', 'Articles', '', '2026-03-07 13:18:38', 'de', NULL, ''),
(470, 'nav_link_49', 'it', 'Articoli', '', '2026-03-07 13:18:38', 'de', NULL, ''),
(471, 'nav_link_50', 'de', 'Artikel', '', '2026-03-07 13:53:55', 'de', NULL, ''),
(472, 'nav_link_50', 'en', 'Articles', '', '2026-03-07 13:53:55', 'de', NULL, ''),
(473, 'nav_link_50', 'it', 'Articoli', '', '2026-03-07 13:53:55', 'de', NULL, ''),
(474, 'nav_link_51', 'de', 'Artikel', 'articles', '2026-03-15 15:01:35', 'de', NULL, ''),
(475, 'nav_link_51', 'en', 'Articles', 'articles', '2026-03-15 15:01:35', 'de', NULL, ''),
(476, 'nav_link_51', 'it', 'Articoli', 'articles', '2026-03-15 15:01:35', 'de', NULL, ''),
(477, 'nav_link_52', 'de', 'Spielserver', 'gametracker', '2026-03-15 15:02:28', 'de', NULL, ''),
(478, 'nav_link_52', 'en', 'Game Servers', 'gametracker', '2026-03-15 15:02:28', 'de', NULL, ''),
(479, 'nav_link_52', 'it', 'Server di gioco', 'gametracker', '2026-03-15 15:02:28', 'de', NULL, ''),
(480, 'nav_link_53', 'de', 'Youtube', 'youtube', '2026-03-15 15:08:37', 'de', NULL, ''),
(481, 'nav_link_53', 'en', 'Youtube', 'youtube', '2026-03-15 15:08:37', 'de', NULL, ''),
(482, 'nav_link_53', 'it', 'Youtube', 'youtube', '2026-03-15 15:08:37', 'de', NULL, ''),
(483, 'nav_link_54', 'de', 'Todo', '', '2026-03-07 18:10:09', 'de', NULL, ''),
(484, 'nav_link_54', 'en', 'Todo', '', '2026-03-07 18:10:09', 'de', NULL, ''),
(485, 'nav_link_54', 'it', 'Todo', '', '2026-03-07 18:10:09', 'de', NULL, ''),
(486, 'nav_link_55', 'de', 'Sponsoren', 'sponsors', '2026-03-15 15:08:06', 'de', NULL, ''),
(487, 'nav_link_55', 'en', 'Sponsors', 'sponsors', '2026-03-15 15:08:06', 'de', NULL, ''),
(488, 'nav_link_55', 'it', 'Sponsor', 'sponsors', '2026-03-15 15:08:06', 'de', NULL, ''),
(546, 'nav_link_69', 'de', 'Letzte Anmeldung', 'lastlogin', '2026-03-15 15:02:33', 'de', NULL, ''),
(547, 'nav_link_69', 'en', 'Last Login', 'lastlogin', '2026-03-15 15:02:33', 'de', NULL, ''),
(548, 'nav_link_69', 'it', 'Ultimi Login', 'lastlogin', '2026-03-15 15:02:33', 'de', NULL, ''),
(549, 'nav_link_70', 'de', 'News', 'news', '2026-03-15 15:05:26', 'de', NULL, ''),
(550, 'nav_link_70', 'en', 'News', 'news', '2026-03-15 15:05:26', 'de', NULL, ''),
(551, 'nav_link_70', 'it', 'Notizie', 'news', '2026-03-15 15:05:26', 'de', NULL, ''),
(552, 'nav_link_71', 'de', 'Shoutbox', '', '2026-03-08 16:55:53', 'de', NULL, ''),
(553, 'nav_link_71', 'en', 'Shoutbox', '', '2026-03-08 16:55:53', 'de', NULL, ''),
(554, 'nav_link_71', 'it', 'Shoutbox', '', '2026-03-08 16:55:53', 'de', NULL, ''),
(555, 'nav_link_72', 'de', 'Gallery', 'gallery', '2026-03-15 15:02:22', 'de', NULL, ''),
(556, 'nav_link_72', 'en', 'Gallery', 'gallery', '2026-03-15 15:02:22', 'de', NULL, ''),
(557, 'nav_link_72', 'it', 'Gallery', 'gallery', '2026-03-15 15:02:22', 'de', NULL, ''),
(558, 'nav_link_73', 'de', 'JoinUs', '', '2026-03-09 20:59:50', 'de', NULL, ''),
(559, 'nav_link_73', 'en', 'JoinUs', '', '2026-03-09 20:59:50', 'de', NULL, ''),
(560, 'nav_link_73', 'it', 'JoinUs', '', '2026-03-09 20:59:50', 'de', NULL, ''),
(561, 'nav_link_74', 'de', 'Preise & Tarife', 'pricing', '2026-03-15 15:05:40', 'de', NULL, ''),
(562, 'nav_link_74', 'en', 'Pricing', 'pricing', '2026-03-15 15:05:40', 'de', NULL, ''),
(563, 'nav_link_74', 'it', 'Pricing', 'pricing', '2026-03-15 15:05:40', 'de', NULL, ''),
(564, 'nav_link_75', 'de', 'Raidplaner', 'raidplaner', '2026-03-15 15:22:00', 'de', NULL, ''),
(565, 'nav_link_75', 'en', 'Raid planner', 'raidplaner', '2026-03-15 15:22:00', 'de', NULL, ''),
(566, 'nav_link_75', 'it', 'Raid planner', 'raidplaner', '2026-03-15 15:22:00', 'de', NULL, ''),
(567, 'nav_link_76', 'de', 'Forum', '', '2026-03-11 20:27:22', 'de', NULL, ''),
(568, 'nav_link_76', 'en', 'Forum', '', '2026-03-11 20:27:22', 'de', NULL, ''),
(569, 'nav_link_76', 'it', 'Forum', '', '2026-03-11 20:27:22', 'de', NULL, ''),
(570, 'nav_link_77', 'de', 'Links', 'links', '2026-03-15 15:04:53', 'de', NULL, ''),
(571, 'nav_link_77', 'en', 'Links', 'links', '2026-03-15 15:04:53', 'de', NULL, ''),
(572, 'nav_link_77', 'it', 'Link', 'links', '2026-03-15 15:04:53', 'de', NULL, '');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `navigation_dashboard_links`
--

CREATE TABLE `navigation_dashboard_links` (
  `linkID` int(11) NOT NULL,
  `catID` int(11) NOT NULL DEFAULT 0,
  `modulname` varchar(255) NOT NULL DEFAULT '',
  `url` varchar(255) NOT NULL DEFAULT '',
  `sort` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `navigation_dashboard_links`
--

INSERT INTO `navigation_dashboard_links` (`linkID`, `catID`, `modulname`, `url`, `sort`) VALUES
(1, 1, 'ac_overview', 'admincenter.php?site=overview', 1),
(2, 1, 'ac_settings', 'admincenter.php?site=settings', 2),
(3, 1, 'ac_dashboard_navigation', 'admincenter.php?site=dashboard_navigation', 3),
(4, 1, 'ac_email', 'admincenter.php?site=email', 4),
(5, 1, 'ac_contact', 'admincenter.php?site=contact', 5),
(6, 1, 'ac_database', 'admincenter.php?site=database', 6),
(7, 1, 'ac_languages', 'admincenter.php?site=languages', 7),
(8, 1, 'ac_editlang', 'admincenter.php?site=editlang', 8),
(9, 1, 'ac_seo_meta', 'admincenter.php?site=seo_meta', 9),
(10, 1, 'ac_update_core', 'admincenter.php?site=update_core', 10),
(11, 2, 'ac_statistic', 'admincenter.php?site=statistic', 1),
(12, 2, 'ac_visitor_statistic', 'admincenter.php?site=visitor_statistic', 2),
(13, 2, 'ac_db_stats', 'admincenter.php?site=db_stats', 3),
(14, 3, 'ac_user_roles', 'admincenter.php?site=user_roles', 1),
(15, 4, 'ac_security_overview', 'admincenter.php?site=security_overview', 1),
(16, 4, 'ac_log_viewer', 'admincenter.php?site=log_viewer', 1),
(17, 6, 'ac_webside_navigation', 'admincenter.php?site=webside_navigation', 1),
(18, 6, 'ac_theme_installer', 'admincenter.php?site=theme_installer', 2),
(19, 6, 'ac_theme', 'admincenter.php?site=theme', 3),
(20, 6, 'ac_stylesheet', 'admincenter.php?site=edit_stylesheet', 4),
(21, 6, 'ac_headstyle', 'admincenter.php?site=headstyle', 5),
(22, 6, 'ac_startpage', 'admincenter.php?site=settings_startpage', 6),
(23, 6, 'ac_static', 'admincenter.php?site=settings_static', 7),
(24, 6, 'ac_imprint', 'admincenter.php?site=settings_imprint', 8),
(25, 6, 'ac_privacy_policy', 'admincenter.php?site=settings_privacy_policy', 9),
(26, 7, 'ac_plugin_manager', 'admincenter.php?site=plugin_manager', 1),
(27, 7, 'ac_plugin_widgets_setting', 'admincenter.php?site=plugin_widgets_setting', 2),
(28, 7, 'ac_plugin_installer', 'admincenter.php?site=plugin_installer', 3),
(29, 8, 'footer', 'admincenter.php?site=admin_footer', 1),
(30, 6, 'navigation', 'admincenter.php?site=admin_navigation_settings', 1),
(31, 6, 'ac_terms_of_service', 'admincenter.php?site=settings_terms_of_service', 10),
(35, 5, 'rules', 'admincenter.php?site=admin_rules', 1),
(37, 3, 'achievements', 'admincenter.php?site=admin_achievements', 1),
(38, 5, 'about', 'admincenter.php?site=admin_about', 1),
(39, 10, 'carousel', 'admincenter.php?site=admin_carousel', 1),
(42, 3, 'userlist', 'admincenter.php?site=admin_userlist', 1),
(43, 13, 'partners', 'admincenter.php?site=admin_partners', 1),
(45, 11, 'discord', 'admincenter.php?site=admin_discord', 1),
(46, 13, 'downloads', 'admincenter.php?site=admin_downloads', 1),
(47, 11, 'twitch', 'admincenter.php?site=admin_twitch', 1),
(48, 11, 'teamspeak', 'admincenter.php?site=admin_teamspeak', 1),
(51, 8, 'articles', 'admincenter.php?site=admin_articles', 1),
(52, 11, 'gametracker', 'admincenter.php?site=admin_gametracker', 1),
(53, 9, 'youtube', 'admincenter.php?site=admin_youtube', 1),
(54, 8, 'todo', 'admincenter.php?site=admin_todo', 1),
(55, 13, 'sponsors', 'admincenter.php?site=admin_sponsors', 1),
(69, 3, 'lastlogin', 'admincenter.php?site=admin_lastlogin', 2),
(70, 8, 'news', 'admincenter.php?site=admin_news', 1),
(71, 11, 'shoutbox', 'admincenter.php?site=admin_shoutbox', 1),
(72, 9, 'gallery', 'admincenter.php?site=admin_gallery', 1),
(73, 5, 'joinus', 'admincenter.php?site=admin_joinus', 1),
(74, 8, 'pricing', 'admincenter.php?site=admin_pricing', 1),
(75, 8, 'raidplaner', 'admincenter.php?site=admin_raidplaner', 1),
(76, 8, 'forum', 'admincenter.php?site=admin_forum', 1),
(77, 13, 'links', 'admincenter.php?site=admin_links', 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `navigation_website_lang`
--

CREATE TABLE `navigation_website_lang` (
  `id` int(11) NOT NULL,
  `content_key` varchar(191) NOT NULL,
  `language` varchar(8) NOT NULL DEFAULT 'de',
  `content` varchar(255) NOT NULL DEFAULT '',
  `modulname` varchar(255) NOT NULL DEFAULT '',
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `lang` varchar(10) NOT NULL DEFAULT 'de',
  `translation` text DEFAULT NULL,
  `name` varchar(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `navigation_website_lang`
--

INSERT INTO `navigation_website_lang` (`id`, `content_key`, `language`, `content`, `modulname`, `updated_at`, `lang`, `translation`, `name`) VALUES
(1, 'nav_main_1', 'de', 'Aktuelles', 'nav_home', '2026-03-03 17:05:01', 'de', NULL, ''),
(2, 'nav_main_1', 'en', 'News', 'nav_home', '2026-03-03 17:05:01', 'de', NULL, ''),
(3, 'nav_main_1', 'it', 'Notizie', 'nav_home', '2026-03-03 17:05:01', 'de', NULL, ''),
(4, 'nav_main_2', 'de', 'Über uns', 'nav_about', '2026-03-03 17:05:01', 'de', NULL, ''),
(5, 'nav_main_2', 'en', 'About us', 'nav_about', '2026-03-03 17:05:01', 'de', NULL, ''),
(6, 'nav_main_2', 'it', 'Chi siamo', 'nav_about', '2026-03-03 17:05:01', 'de', NULL, ''),
(7, 'nav_main_3', 'de', 'Community', 'nav_community', '2026-03-03 17:05:01', 'de', NULL, ''),
(8, 'nav_main_3', 'en', 'Community', 'nav_community', '2026-03-03 17:05:01', 'de', NULL, ''),
(9, 'nav_main_3', 'it', 'Community', 'nav_community', '2026-03-03 17:05:01', 'de', NULL, ''),
(10, 'nav_main_4', 'de', 'Medien', 'nav_media', '2026-03-03 17:05:01', 'de', NULL, ''),
(11, 'nav_main_4', 'en', 'Media', 'nav_media', '2026-03-03 17:05:01', 'de', NULL, ''),
(12, 'nav_main_4', 'it', 'Media', 'nav_media', '2026-03-03 17:05:01', 'de', NULL, ''),
(13, 'nav_main_5', 'de', 'Service', 'nav_service', '2026-03-03 17:05:01', 'de', NULL, ''),
(14, 'nav_main_5', 'en', 'Service', 'nav_service', '2026-03-03 17:05:01', 'de', NULL, ''),
(15, 'nav_main_5', 'it', 'Servizio', 'nav_service', '2026-03-03 17:05:01', 'de', NULL, ''),
(16, 'nav_main_6', 'de', 'Netzwerk', 'nav_network', '2026-03-03 17:05:01', 'de', NULL, ''),
(17, 'nav_main_6', 'en', 'Network', 'nav_network', '2026-03-03 17:05:01', 'de', NULL, ''),
(18, 'nav_main_6', 'it', 'Rete', 'nav_network', '2026-03-03 17:05:01', 'de', NULL, ''),
(37, 'nav_sub_1', 'de', 'Regeln', '', '2026-03-02 19:46:05', 'de', NULL, ''),
(38, 'nav_sub_1', 'en', 'Rules', '', '2026-03-02 19:46:05', 'de', NULL, ''),
(39, 'nav_sub_1', 'it', 'Regole', '', '2026-03-02 19:46:05', 'de', NULL, ''),
(40, 'nav_sub_2', 'de', 'Regeln', '', '2026-03-02 19:56:00', 'de', NULL, ''),
(41, 'nav_sub_2', 'en', 'Rules', '', '2026-03-02 19:56:00', 'de', NULL, ''),
(42, 'nav_sub_2', 'it', 'Regole', '', '2026-03-02 19:56:00', 'de', NULL, ''),
(61, 'nav_sub_0', 'de', 'Regeln', '', '2026-03-03 20:43:00', 'de', NULL, ''),
(62, 'nav_sub_0', 'en', 'Rules', '', '2026-03-03 20:43:00', 'de', NULL, ''),
(63, 'nav_sub_0', 'it', 'Regole', '', '2026-03-03 20:43:00', 'de', NULL, ''),
(64, 'nav_sub_4', 'de', 'Regeln', '', '2026-03-04 18:38:39', 'de', NULL, ''),
(65, 'nav_sub_4', 'en', 'Rules', '', '2026-03-04 18:38:39', 'de', NULL, ''),
(66, 'nav_sub_4', 'it', 'Regole', '', '2026-03-04 18:38:39', 'de', NULL, ''),
(67, 'nav_sub_5', 'de', 'Ãœber uns', '', '2026-03-04 20:11:44', 'de', NULL, ''),
(68, 'nav_sub_5', 'en', 'About Us', '', '2026-03-04 20:11:44', 'de', NULL, ''),
(69, 'nav_sub_5', 'it', 'Chi siamo', '', '2026-03-04 20:11:44', 'de', NULL, ''),
(70, 'nav_sub_6', 'de', 'Errungenschaften', '', '2026-03-04 20:21:11', 'de', NULL, ''),
(71, 'nav_sub_6', 'en', 'Achievements', '', '2026-03-04 20:21:11', 'de', NULL, ''),
(72, 'nav_sub_6', 'it', 'Risultati', '', '2026-03-04 20:21:11', 'de', NULL, ''),
(73, 'nav_sub_7', 'de', 'Ueber uns', 'about', '2026-03-15 15:01:32', 'de', NULL, ''),
(74, 'nav_sub_7', 'en', 'About Us', 'about', '2026-03-15 15:01:32', 'de', NULL, ''),
(75, 'nav_sub_7', 'it', 'Chi siamo', 'about', '2026-03-15 15:01:32', 'de', NULL, ''),
(76, 'nav_sub_8', 'de', 'Mitglieder', '', '2026-03-04 21:51:51', 'de', NULL, ''),
(77, 'nav_sub_8', 'en', 'Members', '', '2026-03-04 21:51:51', 'de', NULL, ''),
(78, 'nav_sub_8', 'it', 'Membri', '', '2026-03-04 21:51:51', 'de', NULL, ''),
(79, 'nav_sub_9', 'de', 'Mitglieder', '', '2026-03-04 21:57:44', 'de', NULL, ''),
(80, 'nav_sub_9', 'en', 'Members', '', '2026-03-04 21:57:44', 'de', NULL, ''),
(81, 'nav_sub_9', 'it', 'Membri', '', '2026-03-04 21:57:44', 'de', NULL, ''),
(82, 'nav_sub_10', 'de', 'Mitglieder', 'userlist', '2026-03-15 15:08:30', 'de', NULL, ''),
(83, 'nav_sub_10', 'en', 'Members', 'userlist', '2026-03-15 15:08:30', 'de', NULL, ''),
(84, 'nav_sub_10', 'it', 'Membri', 'userlist', '2026-03-15 15:08:30', 'de', NULL, ''),
(85, 'nav_sub_11', 'de', 'Counter', 'counter', '2026-03-15 15:01:42', 'de', NULL, ''),
(86, 'nav_sub_11', 'en', 'Counter', 'counter', '2026-03-15 15:01:42', 'de', NULL, ''),
(87, 'nav_sub_11', 'it', 'Contatore', 'counter', '2026-03-15 15:01:42', 'de', NULL, ''),
(88, 'nav_sub_12', 'de', 'Partner', 'partners', '2026-03-15 15:05:33', 'de', NULL, ''),
(89, 'nav_sub_12', 'en', 'Partners', 'partners', '2026-03-15 15:05:33', 'de', NULL, ''),
(90, 'nav_sub_12', 'it', 'Partner', 'partners', '2026-03-15 15:05:33', 'de', NULL, ''),
(91, 'nav_sub_13', 'de', 'Artikel', '', '2026-03-05 20:51:40', 'de', NULL, ''),
(92, 'nav_sub_13', 'en', 'Articles', '', '2026-03-05 20:51:40', 'de', NULL, ''),
(93, 'nav_sub_13', 'it', 'Articoli', '', '2026-03-05 20:51:40', 'de', NULL, ''),
(94, 'nav_sub_14', 'de', 'Discord', '', '2026-03-05 20:58:55', 'de', NULL, ''),
(95, 'nav_sub_14', 'en', 'Discord', '', '2026-03-05 20:58:55', 'de', NULL, ''),
(96, 'nav_sub_14', 'it', 'Discord', '', '2026-03-05 20:58:55', 'de', NULL, ''),
(97, 'nav_sub_15', 'de', 'Download', 'downloads', '2026-03-15 15:01:48', 'de', NULL, ''),
(98, 'nav_sub_15', 'en', 'Download', 'downloads', '2026-03-15 15:01:48', 'de', NULL, ''),
(99, 'nav_sub_15', 'it', 'Download', 'downloads', '2026-03-15 15:01:48', 'de', NULL, ''),
(100, 'nav_sub_16', 'de', 'Twitch', '', '2026-03-06 20:56:42', 'de', NULL, ''),
(101, 'nav_sub_16', 'en', 'Twitch', '', '2026-03-06 20:56:42', 'de', NULL, ''),
(102, 'nav_sub_16', 'it', 'Twitch', '', '2026-03-06 20:56:42', 'de', NULL, ''),
(103, 'nav_sub_17', 'de', 'TeamSpeak', '', '2026-03-07 00:00:48', 'de', NULL, ''),
(104, 'nav_sub_17', 'en', 'TeamSpeak', '', '2026-03-07 00:00:48', 'de', NULL, ''),
(105, 'nav_sub_17', 'it', 'TeamSpeak', '', '2026-03-07 00:00:48', 'de', NULL, ''),
(106, 'nav_sub_18', 'de', 'Artikel', '', '2026-03-07 13:18:38', 'de', NULL, ''),
(107, 'nav_sub_18', 'en', 'Articles', '', '2026-03-07 13:18:38', 'de', NULL, ''),
(108, 'nav_sub_18', 'it', 'Articoli', '', '2026-03-07 13:18:38', 'de', NULL, ''),
(109, 'nav_sub_19', 'de', 'Artikel', '', '2026-03-07 13:53:55', 'de', NULL, ''),
(110, 'nav_sub_19', 'en', 'Articles', '', '2026-03-07 13:53:55', 'de', NULL, ''),
(111, 'nav_sub_19', 'it', 'Articoli', '', '2026-03-07 13:53:55', 'de', NULL, ''),
(112, 'nav_sub_20', 'de', 'Artikel', 'articles', '2026-03-15 15:01:35', 'de', NULL, ''),
(113, 'nav_sub_20', 'en', 'Articles', 'articles', '2026-03-15 15:01:35', 'de', NULL, ''),
(114, 'nav_sub_20', 'it', 'Articoli', 'articles', '2026-03-15 15:01:35', 'de', NULL, ''),
(115, 'nav_sub_21', 'de', 'Spielserver', 'gametracker', '2026-03-15 15:02:28', 'de', NULL, ''),
(116, 'nav_sub_21', 'en', 'Game Servers', 'gametracker', '2026-03-15 15:02:28', 'de', NULL, ''),
(117, 'nav_sub_21', 'it', 'Server di gioco', 'gametracker', '2026-03-15 15:02:28', 'de', NULL, ''),
(118, 'nav_sub_22', 'de', 'Youtube', 'youtube', '2026-03-15 15:08:37', 'de', NULL, ''),
(119, 'nav_sub_22', 'en', 'Youtube', 'youtube', '2026-03-15 15:08:37', 'de', NULL, ''),
(120, 'nav_sub_22', 'it', 'Youtube', 'youtube', '2026-03-15 15:08:37', 'de', NULL, ''),
(121, 'nav_sub_23', 'de', 'Todo', '', '2026-03-07 18:10:09', 'de', NULL, ''),
(122, 'nav_sub_23', 'en', 'Todo', '', '2026-03-07 18:10:09', 'de', NULL, ''),
(123, 'nav_sub_23', 'it', 'Todo', '', '2026-03-07 18:10:09', 'de', NULL, ''),
(124, 'nav_sub_24', 'de', 'Sponsoren', 'sponsors', '2026-03-15 15:08:06', 'de', NULL, ''),
(125, 'nav_sub_24', 'en', 'Sponsors', 'sponsors', '2026-03-15 15:08:06', 'de', NULL, ''),
(126, 'nav_sub_24', 'it', 'Sponsor', 'sponsors', '2026-03-15 15:08:06', 'de', NULL, ''),
(139, 'nav_sub_32', 'de', 'Info', '', '2026-03-07 22:43:31', 'de', NULL, ''),
(144, 'nav_sub_35', 'de', 'Leistung', '', '2026-03-07 22:43:45', 'de', NULL, ''),
(170, 'nav_sub_47', 'de', 'Leistung', '', '2026-03-08 11:22:58', 'de', NULL, ''),
(171, 'nav_sub_47', 'en', 'Services', '', '2026-03-08 11:22:58', 'de', NULL, ''),
(172, 'nav_sub_47', 'it', 'Servizi', '', '2026-03-08 11:22:58', 'de', NULL, ''),
(173, 'nav_sub_48', 'de', 'Info', '', '2026-03-08 11:22:58', 'de', NULL, ''),
(174, 'nav_sub_48', 'en', 'Info', '', '2026-03-08 11:22:58', 'de', NULL, ''),
(175, 'nav_sub_48', 'it', 'Info', '', '2026-03-08 11:22:58', 'de', NULL, ''),
(197, 'nav_sub_56', 'de', 'News', 'news', '2026-03-15 15:05:26', 'de', NULL, ''),
(198, 'nav_sub_56', 'en', 'News', 'news', '2026-03-15 15:05:26', 'de', NULL, ''),
(199, 'nav_sub_56', 'it', 'Notizie', 'news', '2026-03-15 15:05:26', 'de', NULL, ''),
(200, 'nav_sub_57', 'de', 'Shoutbox', '', '2026-03-08 16:55:53', 'de', NULL, ''),
(201, 'nav_sub_57', 'en', 'Shoutbox', '', '2026-03-08 16:55:53', 'de', NULL, ''),
(202, 'nav_sub_57', 'it', 'Shoutbox', '', '2026-03-08 16:55:53', 'de', NULL, ''),
(203, 'nav_sub_58', 'de', 'Live-Besucher', 'live_visitor', '2026-03-15 15:04:59', 'de', NULL, ''),
(204, 'nav_sub_58', 'en', 'Live Visitors', 'live_visitor', '2026-03-15 15:04:59', 'de', NULL, ''),
(205, 'nav_sub_58', 'it', 'Visitatori in tempo reale', 'live_visitor', '2026-03-15 15:04:59', 'de', NULL, ''),
(206, 'nav_sub_59', 'de', 'Gallery', 'gallery', '2026-03-15 15:02:22', 'de', NULL, ''),
(207, 'nav_sub_59', 'en', 'Gallery', 'gallery', '2026-03-15 15:02:22', 'de', NULL, ''),
(208, 'nav_sub_59', 'it', 'Gallery', 'gallery', '2026-03-15 15:02:22', 'de', NULL, ''),
(209, 'nav_sub_60', 'de', 'Join Us', '', '2026-03-09 20:59:50', 'de', NULL, ''),
(210, 'nav_sub_60', 'en', 'Join Us', '', '2026-03-09 20:59:50', 'de', NULL, ''),
(211, 'nav_sub_60', 'it', 'Unisciti', '', '2026-03-09 20:59:50', 'de', NULL, ''),
(212, 'nav_sub_61', 'de', 'Masterliste', '', '2026-03-10 19:26:00', 'de', NULL, ''),
(213, 'nav_sub_61', 'en', 'Game Masterlist', '', '2026-03-10 19:26:00', 'de', NULL, ''),
(214, 'nav_sub_61', 'it', 'Lista giochi', '', '2026-03-10 19:26:00', 'de', NULL, ''),
(215, 'nav_sub_62', 'de', 'Preise & Tarife', 'pricing', '2026-03-15 15:05:40', 'de', NULL, ''),
(216, 'nav_sub_62', 'en', 'Pricing', 'pricing', '2026-03-15 15:05:40', 'de', NULL, ''),
(217, 'nav_sub_62', 'it', 'Pricing', 'pricing', '2026-03-15 15:05:40', 'de', NULL, ''),
(218, 'nav_sub_63', 'de', 'Raids', 'raidplaner', '2026-03-15 15:22:00', 'de', NULL, ''),
(219, 'nav_sub_63', 'en', 'Raids', 'raidplaner', '2026-03-15 15:22:00', 'de', NULL, ''),
(220, 'nav_sub_63', 'it', 'Raids', 'raidplaner', '2026-03-15 15:22:00', 'de', NULL, ''),
(221, 'nav_sub_64', 'de', 'Forum', '', '2026-03-11 20:27:22', 'de', NULL, ''),
(222, 'nav_sub_64', 'en', 'Forum', '', '2026-03-11 20:27:22', 'de', NULL, ''),
(223, 'nav_sub_64', 'it', 'Forum', '', '2026-03-11 20:27:22', 'de', NULL, ''),
(224, 'nav_sub_65', 'de', 'Suche', 'search', '2026-03-15 15:06:06', 'de', NULL, ''),
(225, 'nav_sub_65', 'en', 'Search', 'search', '2026-03-15 15:06:06', 'de', NULL, ''),
(226, 'nav_sub_65', 'it', 'Ricerca', 'search', '2026-03-15 15:06:06', 'de', NULL, ''),
(227, 'nav_sub_66', 'de', 'Links', 'links', '2026-03-15 15:04:53', 'de', NULL, ''),
(228, 'nav_sub_66', 'en', 'Links', 'links', '2026-03-15 15:04:53', 'de', NULL, ''),
(229, 'nav_sub_66', 'it', 'Link', 'links', '2026-03-15 15:04:53', 'de', NULL, ''),
(234, 'nav_sub_68', 'de', 'Leistung', 'about', '2026-03-15 15:01:32', 'de', NULL, ''),
(235, 'nav_sub_68', 'en', 'Services', 'about', '2026-03-15 15:01:32', 'de', NULL, ''),
(236, 'nav_sub_68', 'it', 'Servizi', 'about', '2026-03-15 15:01:32', 'de', NULL, ''),
(237, 'nav_sub_69', 'de', 'Info', 'about', '2026-03-15 15:01:32', 'de', NULL, ''),
(238, 'nav_sub_69', 'en', 'Info', 'about', '2026-03-15 15:01:32', 'de', NULL, ''),
(239, 'nav_sub_69', 'it', 'Info', 'about', '2026-03-15 15:01:32', 'de', NULL, '');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `navigation_website_main`
--

CREATE TABLE `navigation_website_main` (
  `mnavID` int(11) NOT NULL,
  `modulname` varchar(255) NOT NULL DEFAULT '',
  `url` varchar(255) NOT NULL DEFAULT '#',
  `default` tinyint(1) NOT NULL DEFAULT 1,
  `sort` int(11) NOT NULL DEFAULT 0,
  `isdropdown` tinyint(1) NOT NULL DEFAULT 0,
  `windows` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `navigation_website_main`
--

INSERT INTO `navigation_website_main` (`mnavID`, `modulname`, `url`, `default`, `sort`, `isdropdown`, `windows`) VALUES
(1, 'nav_home', '#', 1, 1, 1, 1),
(2, 'nav_about', '#', 1, 2, 1, 1),
(3, 'nav_community', '#', 1, 3, 1, 1),
(4, 'nav_media', '#', 1, 4, 1, 1),
(5, 'nav_service', '#', 1, 5, 1, 1),
(6, 'nav_network', '#', 1, 6, 1, 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `navigation_website_settings`
--

CREATE TABLE `navigation_website_settings` (
  `setting_key` varchar(64) NOT NULL,
  `setting_value` varchar(255) NOT NULL,
  `last_modified` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `navigation_website_settings`
--

INSERT INTO `navigation_website_settings` (`setting_key`, `setting_value`, `last_modified`) VALUES
('chevron_rotate', '1', '2026-03-02 15:58:23'),
('chevron_show', '1', '2026-03-02 15:58:23'),
('dropdown_animation', 'fade', '2026-03-02 15:48:40'),
('dropdown_item_hover', 'surface', '2026-03-02 15:58:23'),
('dropdown_item_padding_x', '16', '2026-03-02 15:58:23'),
('dropdown_item_padding_y', '11', '2026-03-02 15:58:23'),
('dropdown_menu_padding', '8', '2026-03-02 15:58:23'),
('dropdown_radius', '0.5rem', '2026-03-02 15:58:23'),
('dropdown_shadow', '', '2026-03-02 15:58:23'),
('dropdown_style', 'auto', '2026-03-02 15:58:23'),
('dropdown_trigger', 'hover', '2026-03-02 15:58:23'),
('dropdown_width', 'auto', '2026-03-02 15:58:23'),
('logo_center', '0', '2026-03-02 15:48:40'),
('logo_dark', 'logo_dark.png', '2026-03-02 15:48:40'),
('logo_light', 'logo_light.png', '2026-03-02 15:48:40'),
('mobile_breakpoint', 'sm', '2026-03-02 15:48:40'),
('nav_height', '80px', '2026-03-02 15:48:40'),
('navbar_class', 'bg-primary', '2026-03-22 13:01:44'),
('navbar_density', 'normal', '2026-03-02 15:58:23'),
('navbar_modus', 'light', '2026-03-22 13:01:44'),
('navbar_shadow', 'bg-primary', '2026-03-22 13:01:44'),
('navbar_theme', 'light', '2026-03-22 13:01:44'),
('theme_engine_enabled', '2', '2026-03-14 11:33:04');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `navigation_website_sub`
--

CREATE TABLE `navigation_website_sub` (
  `snavID` int(11) NOT NULL,
  `mnavID` int(11) NOT NULL DEFAULT 0,
  `modulname` varchar(255) NOT NULL DEFAULT '',
  `url` varchar(255) NOT NULL DEFAULT '#',
  `sort` int(11) NOT NULL DEFAULT 0,
  `indropdown` tinyint(1) NOT NULL DEFAULT 1,
  `last_modified` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `navigation_website_sub`
--

INSERT INTO `navigation_website_sub` (`snavID`, `mnavID`, `modulname`, `url`, `sort`, `indropdown`, `last_modified`) VALUES
(4, 2, 'rules', 'index.php?site=rules', 1, 1, '2026-03-04 18:38:39'),
(6, 3, 'achievements', 'index.php?site=achievements', 1, 1, '2026-03-04 20:21:11'),
(7, 2, 'about', 'index.php?site=about', 1, 1, '2026-03-04 21:06:29'),
(10, 3, 'userlist', 'index.php?site=userlist', 1, 1, '2026-03-04 22:03:38'),
(11, 5, 'counter', 'index.php?site=counter', 1, 1, '2026-03-05 20:34:04'),
(12, 5, 'partners', 'index.php?site=partners', 1, 1, '2026-03-05 20:38:28'),
(14, 6, 'discord', 'index.php?site=discord', 1, 1, '2026-03-05 20:59:11'),
(15, 5, 'downloads', 'index.php?site=downloads', 1, 1, '2026-03-06 20:02:41'),
(16, 4, 'twitch', 'index.php?site=twitch', 1, 1, '2026-03-06 20:56:42'),
(17, 6, 'teamspeak', 'index.php?site=teamspeak', 1, 1, '2026-03-07 00:00:48'),
(20, 3, 'articles', 'index.php?site=articles', 1, 1, '2026-03-07 13:56:35'),
(21, 6, 'gametracker', 'index.php?site=gametracker', 1, 1, '2026-03-07 16:57:39'),
(22, 4, 'youtube', 'index.php?site=youtube', 1, 1, '2026-03-07 17:50:15'),
(23, 3, 'todo', 'index.php?site=todo', 1, 1, '2026-03-07 18:10:09'),
(24, 5, 'sponsors', 'index.php?site=sponsors', 1, 1, '2026-03-07 20:50:22'),
(32, 2, 'about', 'index.php?site=info', 2, 1, '2026-03-07 22:43:31'),
(35, 2, 'about', 'index.php?site=leistung', 3, 1, '2026-03-07 22:43:45'),
(56, 1, 'news', 'index.php?site=news', 1, 1, '2026-03-15 19:42:28'),
(57, 3, 'shoutbox', 'index.php?site=shoutbox', 1, 1, '2026-03-08 16:55:53'),
(58, 3, 'live_visitor', 'index.php?site=live_visitor', 1, 1, '2026-03-08 19:05:07'),
(59, 4, 'gallery', 'index.php?site=gallery', 1, 1, '2026-03-09 17:44:50'),
(60, 3, 'joinus', 'index.php?site=joinus', 3, 1, '2026-03-09 20:59:50'),
(61, 6, 'masterlist', 'index.php?site=masterlist', 1, 1, '2026-03-10 19:26:00'),
(62, 1, 'pricing', 'index.php?site=pricing', 1, 1, '2026-03-10 19:29:19'),
(63, 3, 'raidplaner', 'index.php?site=raidplaner', 1, 1, '2026-03-11 20:25:08'),
(64, 3, 'forum', 'index.php?site=forum', 1, 1, '2026-03-11 20:27:22'),
(65, 5, 'search', 'index.php?site=search', 1, 1, '2026-03-11 21:38:19'),
(66, 5, 'links', 'index.php?site=links', 1, 1, '2026-03-12 18:47:28'),
(68, 2, 'leistung', 'index.php?site=leistung', 2, 1, '2026-03-15 15:01:32'),
(69, 2, 'info', 'index.php?site=info', 3, 1, '2026-03-15 15:01:32');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `token` char(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  `used` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `password_resets`
--

INSERT INTO `password_resets` (`id`, `userID`, `token`, `expires_at`, `used`, `created_at`) VALUES
(1, 2, '99a28b1b48d58c1785b76678bfbbe9f91ac9d07f616359f6817692422c0c65a7', '2026-03-08 18:31:00', 1, '2026-03-08 17:31:00');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `password_reset_attempts`
--

CREATE TABLE `password_reset_attempts` (
  `id` int(11) NOT NULL,
  `ip` varchar(45) NOT NULL,
  `attempts` int(11) NOT NULL DEFAULT 1,
  `last_attempt` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `password_reset_attempts`
--

INSERT INTO `password_reset_attempts` (`id`, `ip`, `attempts`, `last_attempt`) VALUES
(1, '94.31.75.87', 1, '2026-03-08 17:31:00');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `plugins_about`
--

CREATE TABLE `plugins_about` (
  `id` int(11) NOT NULL,
  `content_key` varchar(50) NOT NULL,
  `language` char(2) NOT NULL,
  `content` mediumtext NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `plugins_about`
--

INSERT INTO `plugins_about` (`id`, `content_key`, `language`, `content`, `updated_at`) VALUES
(1, 'title', 'de', 'Über uns', '2026-03-04 21:23:46'),
(2, 'title', 'en', 'About us', '2026-03-04 21:23:46'),
(3, 'title', 'it', 'Chi siamo', '2026-03-04 21:23:46'),
(4, 'intro', 'de', 'Willkommen auf unserer Website! Wir freuen uns, dir hier einen Einblick in das geben zu können, was hinter **nexpell 1.0** steckt – der modernen, modularen Weiterentwicklung von Webspell-RM. Entdecke eine neue Generation von Content-Management-Funktionen, kombiniert mit zeitgemäßem Design und höchster Flexibilität.', '2026-03-04 21:23:46'),
(5, 'intro', 'en', 'Welcome to our website! We\'re happy to give you an insight into **nexpell 1.0** – the modern, modular evolution of Webspell-RM. Discover a new generation of content management features, combined with contemporary design and maximum flexibility.', '2026-03-04 21:23:46'),
(6, 'intro', 'it', 'Benvenuto sul nostro sito web! Siamo felici di offrirti una panoramica su **nexpell 1.0** – l\'evoluzione moderna e modulare di Webspell-RM. Scopri una nuova generazione di funzionalità per la gestione dei contenuti, con un design attuale e la massima flessibilità.', '2026-03-04 21:23:46'),
(7, 'history', 'de', '<div>Webspell-RM war über viele Jahre ein beliebtes und zuverlässiges System für Clan- und Community-Webseiten. Die ersten Schritte zur Modernisierung begannen bereits 2018 – mit dem Ziel, die Benutzerfreundlichkeit zu verbessern, die Leistung zu steigern und die Flexibilität für verschiedenste Anwendungsbereiche zu erhöhen. In einem kontinuierlichen Prozess wurde das System technisch und gestalterisch weiterentwickelt und optimiert.</div><div>Mit der Version 2.1.6 fiel schließlich die Entscheidung, Webspell-RM neu zu denken. Es war an der Zeit, veraltete Strukturen hinter sich zu lassen und Platz für eine moderne, sichere und zukunftsorientierte Plattform zu schaffen. So wurde die Basis für ein komplett neues System gelegt: **nexpell**.</div><div>**nexpell 1.0** ist nicht nur ein einfaches Update, sondern ein umfassender Neustart. Das CMS wurde von Grund auf neu strukturiert und modular aufgebaut – mit einem modernen, responsiven Design, aktueller Technik, verbesserter Performance und einem besonderen Fokus auf Sicherheit und Erweiterbarkeit. Damit richtet sich nexpell sowohl an Entwickler als auch an Community-Manager, die auf ein verlässliches, flexibles und zukunftssicheres System setzen wollen.</div>', '2026-03-04 21:23:46'),
(8, 'history', 'en', 'Webspell-RM was a popular and reliable system for clan and community websites for many years. The first steps toward modernization began back in 2018, aiming to improve usability, boost performance, and enhance flexibility for various use cases. Over time, the system was continually improved and refined both technically and visually.&lt;br /&gt;\r\nWith version 2.1.6, a conscious decision was made to completely rethink Webspell-RM. It was time to leave outdated structures behind and create space for a modern, secure, and future-proof platform. This laid the foundation for an entirely new system: **nexpell**.&lt;br /&gt;&lt;br /&gt;\r\n**nexpell 1.0** is not just an update – it’s a complete reboot. The CMS has been restructured from the ground up with a modular architecture, a responsive and modern design, cutting-edge technology, enhanced performance, and a strong focus on security and extensibility. nexpell is built for both developers and community managers who want a reliable, flexible, and future-ready system.', '2026-03-04 21:23:46'),
(9, 'history', 'it', 'Per molti anni, Webspell-RM è stato un sistema popolare e affidabile per i siti web di clan e community. I primi passi verso una modernizzazione sono stati compiuti già nel 2018, con l\'obiettivo di migliorare l\'usabilità, aumentare le prestazioni e rendere la piattaforma più flessibile per diversi scenari d\'uso. Nel tempo, il sistema è stato continuamente migliorato e ottimizzato dal punto di vista tecnico e visivo.&lt;br /&gt;&lt;br /&gt;\r\nCon la versione 2.1.6, è stata presa una decisione consapevole: ripensare completamente Webspell-RM. Era giunto il momento di lasciarsi alle spalle strutture obsolete e creare una piattaforma moderna, sicura e proiettata verso il futuro. È così che è nato **nexpell**.&lt;br /&gt;&lt;br /&gt;\r\n**nexpell 1.0** non è un semplice aggiornamento, ma un nuovo inizio completo. Il CMS è stato ristrutturato dalle fondamenta, con un\'architettura modulare, un design moderno e reattivo, tecnologie all\'avanguardia, prestazioni migliorate e un forte focus sulla sicurezza e l\'estensibilità. nexpell è', '2026-03-04 21:23:46'),
(10, 'core_values', 'de', 'Mit **nexpell** verfolgen wir eine klare Vision: Wir glauben an Open Source, an Transparenz und an die Kraft einer starken Community. Unser Ziel ist es, ein System zu schaffen, das Entwicklern und Community-Managern gleichermaßen hilft – durch einfache Bedienbarkeit, hohe Flexibilität und eine zukunftssichere Architektur. nexpell ist mehr als nur ein CMS – es ist eine Plattform von der Community für die Community.', '2026-03-04 21:23:46'),
(11, 'core_values', 'en', 'With **nexpell**, we pursue a clear vision: We believe in open source, transparency, and the power of a strong community. Our goal is to provide a system that supports both developers and community managers – with ease of use, high flexibility, and a future-proof architecture. nexpell is more than just a CMS – it’s a platform built by the community, for the community.', '2026-03-04 21:23:46'),
(12, 'core_values', 'it', 'Con **nexpell** perseguiamo una visione chiara: crediamo nell\'open source, nella trasparenza e nella forza di una community solida. Il nostro obiettivo è offrire un sistema che supporti sia gli sviluppatori che i gestori delle community – con facilità d\'uso, grande flessibilità e un\'architettura a prova di futuro. nexpell non è solo un CMS – è una piattaforma creata dalla community per la community.', '2026-03-04 21:23:46'),
(13, 'team', 'de', 'Hinter **nexpell** steht ein kleines, engagiertes Team aus freiwilligen Entwicklern, Designern und Testern. Uns verbindet die Leidenschaft für moderne Webtechnologien, Gaming-Communities und sauberes, nachhaltiges Code-Design. Jeder Beitrag zählt – denn nexpell lebt von der Community und dem gemeinsamen Anspruch, ein System zu schaffen, das begeistert.', '2026-03-04 21:23:46'),
(14, 'team', 'en', 'Behind **nexpell** is a small, passionate team of volunteer developers, designers, and testers. We are united by our love for modern web technologies, gaming communities, and clean, sustainable code design. Every contribution matters – because nexpell thrives on community spirit and a shared vision to build something outstanding.', '2026-03-04 21:23:46'),
(15, 'team', 'it', 'Dietro **nexpell** c\'è un piccolo team appassionato di sviluppatori, designer e tester volontari. Ci unisce l\'amore per le tecnologie web moderne, le community di gaming e un codice pulito e sostenibile. Ogni contributo conta – perché nexpell vive grazie allo spirito di collaborazione e a una visione condivisa.', '2026-03-04 21:23:46'),
(16, 'cta', 'de', 'Du willst dich einbringen, Feedback geben oder ein Plugin für **nexpell** entwickeln? Dann werde Teil der Community – kontaktiere uns direkt oder schau auf GitHub vorbei. Wir freuen uns über jeden Beitrag!', '2026-03-04 21:23:46'),
(17, 'cta', 'en', 'Want to get involved, share feedback, or build a plugin for **nexpell**? Join the community – reach out to us or visit our GitHub. We welcome every contribution!', '2026-03-04 21:23:46'),
(18, 'cta', 'it', 'Vuoi partecipare, inviare un feedback o sviluppare un plugin per **nexpell**? Unisciti alla community – contattaci direttamente o visita il nostro GitHub. Ogni contributo è il benvenuto!', '2026-03-04 21:23:46'),
(19, 'image1', 'de', 'intro.jpg', '2026-03-04 21:23:46'),
(20, 'image1', 'en', 'intro.jpg', '2026-03-04 21:23:46'),
(21, 'image1', 'it', 'intro.jpg', '2026-03-04 21:23:46'),
(22, 'image2', 'de', 'history.jpg', '2026-03-04 21:23:46'),
(23, 'image2', 'en', 'history.jpg', '2026-03-04 21:23:46'),
(24, 'image2', 'it', 'history.jpg', '2026-03-04 21:23:46'),
(25, 'image3', 'de', 'team.jpg', '2026-03-04 21:23:46'),
(26, 'image3', 'en', 'team.jpg', '2026-03-04 21:23:46'),
(27, 'image3', 'it', 'team.jpg', '2026-03-04 21:23:46');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `plugins_about_legacy`
--

CREATE TABLE `plugins_about_legacy` (
  `id` int(11) NOT NULL,
  `title` text NOT NULL,
  `intro` text NOT NULL,
  `history` text NOT NULL,
  `core_values` text NOT NULL,
  `team` text NOT NULL,
  `cta` text NOT NULL,
  `image1` varchar(255) NOT NULL DEFAULT '',
  `image2` varchar(255) NOT NULL DEFAULT '',
  `image3` varchar(255) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `plugins_about_legacy`
--

INSERT INTO `plugins_about_legacy` (`id`, `title`, `intro`, `history`, `core_values`, `team`, `cta`, `image1`, `image2`, `image3`) VALUES
(1, '[[lang:de]]Über uns[[lang:en]]About us[[lang:it]]Chi siamo', '[[lang:de]]Willkommen auf unserer Website! Wir freuen uns, dir hier einen Einblick in das geben zu können, was hinter **nexpell 1.0** steckt – der modernen, modularen Weiterentwicklung von Webspell-RM. Entdecke eine neue Generation von Content-Management-Funktionen, kombiniert mit zeitgemäßem Design und höchster Flexibilität. [[lang:en]]Welcome to our website! We\'re happy to give you an insight into **nexpell 1.0** – the modern, modular evolution of Webspell-RM. Discover a new generation of content management features, combined with contemporary design and maximum flexibility. [[lang:it]]Benvenuto sul nostro sito web! Siamo felici di offrirti una panoramica su **nexpell 1.0** – l\'evoluzione moderna e modulare di Webspell-RM. Scopri una nuova generazione di funzionalità per la gestione dei contenuti, con un design attuale e la massima flessibilità.', '[[lang:de]]Webspell-RM war über viele Jahre ein beliebtes und zuverlässiges System für Clan- und Community-Webseiten. Die ersten Schritte zur Modernisierung begannen bereits 2018 – mit dem Ziel, die Benutzerfreundlichkeit zu verbessern, die Leistung zu steigern und die Flexibilität für verschiedenste Anwendungsbereiche zu erhöhen. In einem kontinuierlichen Prozess wurde das System technisch und gestalterisch weiterentwickelt und optimiert.<br /><br />\r\nMit der Version 2.1.6 fiel schließlich die Entscheidung, Webspell-RM neu zu denken. Es war an der Zeit, veraltete Strukturen hinter sich zu lassen und Platz für eine moderne, sichere und zukunftsorientierte Plattform zu schaffen. So wurde die Basis für ein komplett neues System gelegt: **nexpell**.<br /><br />\r\n**nexpell 1.0** ist nicht nur ein einfaches Update, sondern ein umfassender Neustart. Das CMS wurde von Grund auf neu strukturiert und modular aufgebaut – mit einem modernen, responsiven Design, aktueller Technik, verbesserter Performance und einem besonderen Fokus auf Sicherheit und Erweiterbarkeit. Damit richtet sich nexpell sowohl an Entwickler als auch an Community-Manager, die auf ein verlässliches, flexibles und zukunftssicheres System setzen wollen. [[lang:en]]Webspell-RM was a popular and reliable system for clan and community websites for many years. The first steps toward modernization began back in 2018, aiming to improve usability, boost performance, and enhance flexibility for various use cases. Over time, the system was continually improved and refined both technically and visually.<br />\r\nWith version 2.1.6, a conscious decision was made to completely rethink Webspell-RM. It was time to leave outdated structures behind and create space for a modern, secure, and future-proof platform. This laid the foundation for an entirely new system: **nexpell**.<br /><br />\r\n**nexpell 1.0** is not just an update – it’s a complete reboot. The CMS has been restructured from the ground up with a modular architecture, a responsive and modern design, cutting-edge technology, enhanced performance, and a strong focus on security and extensibility. nexpell is built for both developers and community managers who want a reliable, flexible, and future-ready system. [[lang:it]]Per molti anni, Webspell-RM è stato un sistema popolare e affidabile per i siti web di clan e community. I primi passi verso una modernizzazione sono stati compiuti già nel 2018, con l\'obiettivo di migliorare l\'usabilità, aumentare le prestazioni e rendere la piattaforma più flessibile per diversi scenari d\'uso. Nel tempo, il sistema è stato continuamente migliorato e ottimizzato dal punto di vista tecnico e visivo.<br /><br />\r\nCon la versione 2.1.6, è stata presa una decisione consapevole: ripensare completamente Webspell-RM. Era giunto il momento di lasciarsi alle spalle strutture obsolete e creare una piattaforma moderna, sicura e proiettata verso il futuro. È così che è nato **nexpell**.<br /><br />\r\n**nexpell 1.0** non è un semplice aggiornamento, ma un nuovo inizio completo. Il CMS è stato ristrutturato dalle fondamenta, con un\'architettura modulare, un design moderno e reattivo, tecnologie all\'avanguardia, prestazioni migliorate e un forte focus sulla sicurezza e l\'estensibilità. nexpell è', '[[lang:de]]Mit **nexpell** verfolgen wir eine klare Vision: Wir glauben an Open Source, an Transparenz und an die Kraft einer starken Community. Unser Ziel ist es, ein System zu schaffen, das Entwicklern und Community-Managern gleichermaßen hilft – durch einfache Bedienbarkeit, hohe Flexibilität und eine zukunftssichere Architektur. nexpell ist mehr als nur ein CMS – es ist eine Plattform von der Community für die Community. [[lang:en]]With **nexpell**, we pursue a clear vision: We believe in open source, transparency, and the power of a strong community. Our goal is to provide a system that supports both developers and community managers – with ease of use, high flexibility, and a future-proof architecture. nexpell is more than just a CMS – it’s a platform built by the community, for the community. [[lang:it]]Con **nexpell** perseguiamo una visione chiara: crediamo nell\'open source, nella trasparenza e nella forza di una community solida. Il nostro obiettivo è offrire un sistema che supporti sia gli sviluppatori che i gestori delle community – con facilità d\'uso, grande flessibilità e un\'architettura a prova di futuro. nexpell non è solo un CMS – è una piattaforma creata dalla community per la community.', '[[lang:de]]Hinter **nexpell** steht ein kleines, engagiertes Team aus freiwilligen Entwicklern, Designern und Testern. Uns verbindet die Leidenschaft für moderne Webtechnologien, Gaming-Communities und sauberes, nachhaltiges Code-Design. Jeder Beitrag zählt – denn nexpell lebt von der Community und dem gemeinsamen Anspruch, ein System zu schaffen, das begeistert. [[lang:en]]Behind **nexpell** is a small, passionate team of volunteer developers, designers, and testers. We are united by our love for modern web technologies, gaming communities, and clean, sustainable code design. Every contribution matters – because nexpell thrives on community spirit and a shared vision to build something outstanding. [[lang:it]]Dietro **nexpell** c\'è un piccolo team appassionato di sviluppatori, designer e tester volontari. Ci unisce l\'amore per le tecnologie web moderne, le community di gaming e un codice pulito e sostenibile. Ogni contributo conta – perché nexpell vive grazie allo spirito di collaborazione e a una visione condivisa.', '[[lang:de]]Du willst dich einbringen, Feedback geben oder ein Plugin für **nexpell** entwickeln? Dann werde Teil der Community – kontaktiere uns direkt oder schau auf GitHub vorbei. Wir freuen uns über jeden Beitrag! [[lang:en]]Want to get involved, share feedback, or build a plugin for **nexpell**? Join the community – reach out to us or visit our GitHub. We welcome every contribution! [[lang:it]]Vuoi partecipare, inviare un feedback o sviluppare un plugin per **nexpell**? Unisciti alla community – contattaci direttamente o visita il nostro GitHub. Ogni contributo è il benvenuto!', 'intro.jpg', 'history.jpg', 'team.jpg');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `plugins_achievements`
--

CREATE TABLE `plugins_achievements` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL DEFAULT 0,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `type` enum('level','points','role','activity_count','category_points','registration_time','bonus_points','manual') NOT NULL DEFAULT 'level',
  `trigger_value` varchar(255) NOT NULL,
  `trigger_condition` varchar(255) DEFAULT NULL,
  `image` varchar(255) NOT NULL,
  `is_standalone` tinyint(1) NOT NULL DEFAULT 0,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `show_in_overview` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1 = wird angezeigt, 0 = wird verborgen',
  `allow_html` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `plugins_achievements`
--

INSERT INTO `plugins_achievements` (`id`, `category_id`, `name`, `description`, `type`, `trigger_value`, `trigger_condition`, `image`, `is_standalone`, `sort_order`, `show_in_overview`, `allow_html`) VALUES
(1, 0, 'Admin', '{clan_name} Admin', 'role', 'Admin', '', 'admin.png', 1, 0, 1, 0),
(2, 0, 'VIP', 'VIP User', 'manual', '', '', 'vip.png', 0, 0, 0, 0),
(3, 0, 'Punkteregen', 'Ein Admin hat es gut mit dir gemeint und dir {points} Punkte geschenkt.', 'bonus_points', '1', '', 'sternenregen.png', 0, 0, 0, 0),
(4, 3, 'Moderator', 'Moderator', 'role', 'Moderator', '', 'moderator.png', 1, 0, 1, 0),
(5, 1, 'Anfänger', 'Du hast Level 5 erreicht', 'level', '5', '', 'anfaenger.png', 0, 0, 1, 0),
(6, 1, 'Fortgeschrittener', 'Du hast Level 25 erreicht', 'level', '25', '', 'fortgeschrittener.png', 0, 0, 1, 0),
(7, 1, 'Erfahrener', 'Du hast Level 50 erreicht', 'level', '50', '', 'erfahrener.png', 0, 0, 1, 0),
(8, 1, 'Legendär', 'Du hast Level 100 erreicht', 'level', '100', '', 'legendaer.png', 0, 0, 1, 0),
(9, 4, 'Schreiberling', 'Du hast deine ersten fünf Artikel verfasst', 'activity_count', '5', 'Artikel', 'articles_bronze.png', 0, 0, 1, 0),
(10, 4, 'Author', 'Du hast 25 Artikel geschrieben', 'activity_count', '25', 'Artikel', 'articles_silver.png', 0, 0, 1, 0),
(11, 4, 'Bestseller Schreiber', 'Du hast 75 Artikel verfasst', 'activity_count', '75', 'Artikel', 'articles_gold.png', 0, 0, 1, 0),
(12, 4, 'Festplattenbelaster', 'Du hast 25 Dateien heruntergeladen', 'activity_count', '25', 'Downloads', 'downloads_bronze.png', 0, 0, 1, 0),
(13, 4, 'Terabytejäger', 'Du hast 75 Dateien heruntergeladen', 'activity_count', '75', 'Downloads', 'downloads_gold.png', 0, 0, 1, 0),
(14, 4, 'Aktiver Poster', 'Du hast 15 Forumbeiträge geschrieben', 'activity_count', '15', 'Forum', 'forum_bronze.png', 0, 0, 1, 0),
(15, 4, 'Gehört zum Inventar', 'Du hast 50 Forumposts geschrieben', 'activity_count', '50', 'Forum', 'forum_silver.png', 0, 0, 1, 0),
(16, 4, 'Forumlegende', 'Du hast 100 Forumposts geschrieben', 'activity_count', '100', 'Forum', 'forum_gold.png', 0, 0, 1, 0),
(17, 4, 'Interessent', 'Du hast 15 Kommentare geschrieben', 'activity_count', '15', 'Kommentare', 'comments_bronze.png', 0, 0, 1, 0),
(18, 4, 'Diskussionsfreudig', 'Du hast 50 Kommentare geschrieben', 'activity_count', '50', 'Kommentare', 'comments_silver.png', 0, 0, 1, 0),
(19, 4, 'Immer am Start', 'Du hast 100 Kommentare geschrieben', 'activity_count', '100', 'Kommentare', 'comments_gold.png', 0, 0, 1, 0),
(20, 4, 'Daumenzücker', 'Du hast 20 Likes vergeben', 'activity_count', '20', 'Likes', 'likes_bronze.png', 0, 0, 1, 0),
(21, 4, 'Die ganze Hand', 'Du hast 75 Likes vergeben', 'activity_count', '75', 'Likes', 'likes_gold.png', 0, 0, 1, 0),
(22, 2, 'Sammler', 'Du hast 2500 Punkte gesammelt', 'points', '2500', '', 'sammler.png', 1, 0, 1, 0),
(23, 2, 'Schatzjäger', 'Du hast im Forum 5000 Punkte verdient', 'category_points', '5000', 'Forum', 'treasure.png', 1, 0, 1, 0),
(24, 4, 'Flash', 'Du bist ein Jahr dabei', 'registration_time', '1', 'years', 'flash.png', 1, 0, 1, 0),
(25, 2, 'Seltener Diamant', 'Du hast 50000 Punkte gesammelt', 'points', '50000', '', 'diamant_selten.png', 1, 0, 1, 0),
(26, 4, 'Designer', 'Designer', 'role', 'Designer', '', 'designer.png', 0, 0, 1, 0);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `plugins_achievements_admin_log`
--

CREATE TABLE `plugins_achievements_admin_log` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `log_type` enum('manual_award','bonus_points') NOT NULL,
  `related_id` int(11) DEFAULT NULL COMMENT 'Für achievement_id bei manual_award',
  `value` int(11) DEFAULT NULL COMMENT 'Für die Punktzahl bei bonus_points',
  `timestamp` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `plugins_achievements_admin_log`
--

INSERT INTO `plugins_achievements_admin_log` (`id`, `user_id`, `admin_id`, `log_type`, `related_id`, `value`, `timestamp`) VALUES
(4, 2, 1, 'manual_award', 28, NULL, '2025-08-18 13:03:59'),
(12, 3, 2, 'manual_award', 28, NULL, '2025-08-18 13:33:52'),
(15, 2, 1, 'bonus_points', NULL, 999, '2025-08-19 17:30:00'),
(16, 2, 1, 'manual_award', 24, NULL, '2025-08-19 17:33:52');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `plugins_achievements_categories`
--

CREATE TABLE `plugins_achievements_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `plugins_achievements_categories`
--

INSERT INTO `plugins_achievements_categories` (`id`, `name`, `description`) VALUES
(2, 'Level', 'Hier kommen die Level Achievements rein'),
(3, 'Punkte', 'Hier kommen die Punkte Achievements rein'),
(4, 'Rollen', 'Achievements zu Rollen');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `plugins_achievements_settings`
--

CREATE TABLE `plugins_achievements_settings` (
  `setting_key` varchar(255) NOT NULL DEFAULT '',
  `setting_value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `plugins_achievements_settings`
--

INSERT INTO `plugins_achievements_settings` (`setting_key`, `setting_value`) VALUES
('admin_bonus_award_limit', '1'),
('custom_locked_icon', 'locked.png'),
('hide_locked_icon', 'no'),
('max_bonus_points', '2000'),
('points_per_level', '100'),
('weight_Artikel', '10'),
('weight_Clan-Regeln', '5'),
('weight_Downloads', '2'),
('weight_Forum', '2'),
('weight_Forum-Likes', '3'),
('weight_Forum-Posts', '3'),
('weight_Forum-Themen', '5'),
('weight_Kommentare', '2'),
('weight_Likes', '2'),
('weight_Links', '5'),
('weight_Logins', '2'),
('weight_News', '5'),
('weight_Partners', '5'),
('weight_Ratings', '5'),
('weight_Sponsoren', '5'),
('weight_ToDo', '3');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `plugins_articles`
--

CREATE TABLE `plugins_articles` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL DEFAULT 0,
  `title` varchar(255) NOT NULL DEFAULT '',
  `content` text NOT NULL,
  `slug` varchar(255) NOT NULL DEFAULT '',
  `banner_image` varchar(255) NOT NULL DEFAULT '',
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `updated_at` int(14) NOT NULL DEFAULT 0,
  `userID` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `rating` int(11) NOT NULL DEFAULT 0,
  `points` int(11) NOT NULL DEFAULT 0,
  `votes` int(11) NOT NULL DEFAULT 0,
  `views` int(11) NOT NULL DEFAULT 0,
  `allow_comments` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `plugins_articles`
--

INSERT INTO `plugins_articles` (`id`, `category_id`, `title`, `content`, `slug`, `banner_image`, `sort_order`, `updated_at`, `userID`, `is_active`, `rating`, `points`, `votes`, `views`, `allow_comments`) VALUES
(1, 1, 'Rechtliche Hinweise aktualisiert', 'sfsfsf', 'rechtliche-hinweise-aktualisiert', '69ac20b67bc67.jpg', 0, 1772888246, 1, 1, 0, 0, 0, 12, 0);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `plugins_articles_categories`
--

CREATE TABLE `plugins_articles_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `plugins_articles_categories`
--

INSERT INTO `plugins_articles_categories` (`id`, `name`, `description`, `sort_order`) VALUES
(1, 'Installation', 'ssfsf', 0);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `plugins_articles_comments`
--

CREATE TABLE `plugins_articles_comments` (
  `commentID` int(11) NOT NULL,
  `parentID` int(11) NOT NULL DEFAULT 0,
  `type` char(2) NOT NULL DEFAULT '',
  `userID` int(11) NOT NULL DEFAULT 0,
  `nickname` varchar(255) NOT NULL DEFAULT '',
  `date` int(14) NOT NULL DEFAULT 0,
  `comments` text NOT NULL,
  `homepage` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `ip` varchar(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `plugins_articles_settings`
--

CREATE TABLE `plugins_articles_settings` (
  `articlessetID` int(11) NOT NULL,
  `articles` int(11) NOT NULL,
  `articleschars` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Daten für Tabelle `plugins_articles_settings`
--

INSERT INTO `plugins_articles_settings` (`articlessetID`, `articles`, `articleschars`) VALUES
(1, 4, 100);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `plugins_carousel`
--

CREATE TABLE `plugins_carousel` (
  `id` int(11) NOT NULL,
  `type` enum('sticky','parallax','agency','carousel') NOT NULL,
  `link` varchar(255) DEFAULT NULL,
  `media_type` enum('image','video') NOT NULL,
  `media_file` varchar(255) DEFAULT NULL,
  `visible` tinyint(1) DEFAULT 1,
  `sort` int(11) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `plugins_carousel`
--

INSERT INTO `plugins_carousel` (`id`, `type`, `link`, `media_type`, `media_file`, `visible`, `sort`, `created_at`) VALUES
(1, 'sticky', 'https://www.nexpell.de', 'image', 'block_687148bb0318b.jpg', 1, 0, '2025-07-11 19:24:11'),
(2, 'parallax', 'https://www.nexpell.de', 'image', 'block_6871494833ec1.jpg', 1, 0, '2025-07-11 19:26:32'),
(3, 'agency', 'https://www.nexpell.de', 'image', 'block_687149651d571.jpg', 1, 0, '2025-07-11 19:27:01'),
(4, 'carousel', 'https://www.nexpell.de', 'image', 'block_687149d478869.jpg', 1, 0, '2025-07-11 19:28:52'),
(5, 'carousel', 'https://www.nexpell.de', 'image', 'block_687149e906f43.jpg', 1, 0, '2025-07-11 19:29:13'),
(6, 'carousel', 'https://www.nexpell.de', 'image', 'block_687149fd5a1af.jpg', 1, 0, '2025-07-11 19:29:33'),
(7, 'carousel', 'https://www.nexpell.de', 'image', 'block_68714d40abe62.jpg', 1, 0, '2025-07-11 19:29:57'),
(8, 'carousel', 'https://www.nexpell.de', 'video', 'block_68714a4106e25.mp4', 1, 0, '2025-07-11 19:30:41');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `plugins_carousel_lang`
--

CREATE TABLE `plugins_carousel_lang` (
  `id` int(11) NOT NULL,
  `content_key` varchar(80) NOT NULL,
  `language` char(2) NOT NULL,
  `content` mediumtext NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `plugins_carousel_lang`
--

INSERT INTO `plugins_carousel_lang` (`id`, `content_key`, `language`, `content`, `updated_at`) VALUES
(1, 'carousel_1_title', 'de', 'ne<span>x</span>pell - Das moderne CMS fuer flexible Webentwicklung', '2026-03-15 15:01:39'),
(2, 'carousel_1_title', 'en', 'ne<span>x</span>pell - The modern CMS for flexible web development', '2026-03-15 15:01:39'),
(3, 'carousel_1_title', 'it', 'ne<span>x</span>pell - Il CMS moderno per uno sviluppo web flessibile', '2026-03-15 15:01:39'),
(4, 'carousel_1_subtitle', 'de', 'nexpell kombiniert Benutzerfreundlichkeit, Performance und Erweiterbarkeit in einem schlanken, modernen Content-Management-System.', '2026-03-15 15:01:39'),
(5, 'carousel_1_subtitle', 'en', 'nexpell combines user-friendliness, performance, and extensibility in a sleek, modern content management system.', '2026-03-15 15:01:39'),
(6, 'carousel_1_subtitle', 'it', 'nexpell combina facilita d\'uso, prestazioni ed estensibilita in un sistema di gestione dei contenuti moderno e snello.', '2026-03-15 15:01:39'),
(7, 'carousel_1_description', 'de', 'Modular, flexibel und leistungsstark - so gestaltest du moderne Websites ohne Grenzen.', '2026-03-15 15:01:39'),
(8, 'carousel_1_description', 'en', 'Modular, flexible, and powerful - this is how you create modern websites without limits.', '2026-03-15 15:01:39'),
(9, 'carousel_1_description', 'it', 'Modulare, flessibile e potente - cosi crei siti web moderni senza limiti.', '2026-03-15 15:01:39'),
(19, 'carousel_8_title', 'en', 'ne<span>x</span>pell - The modern CMS for flexible web development', '2026-03-08 14:20:00'),
(20, 'carousel_8_title', 'de', 'ne<span>x</span>pell - Das moderne CMS fuer flexible Webentwicklung', '2026-03-08 14:20:00'),
(21, 'carousel_8_title', 'it', 'ne<span>x</span>pell - Il CMS moderno per uno sviluppo web flessibile', '2026-03-08 14:20:00'),
(22, 'carousel_8_subtitle', 'en', 'nexpell combines user-friendliness, performance, and extensibility in a sleek, modern content management system.', '2026-03-08 14:20:00'),
(23, 'carousel_8_subtitle', 'de', 'nexpell kombiniert Benutzerfreundlichkeit, Performance und Erweiterbarkeit in einem schlanken, modernen Content-Management-System.', '2026-03-08 14:20:00'),
(24, 'carousel_8_subtitle', 'it', 'nexpell combina facilita d&apos;uso, prestazioni ed estensibilita in un sistema di gestione dei contenuti moderno e snello.', '2026-03-08 14:20:00'),
(25, 'carousel_8_description', 'en', 'Modular, flexible, and powerful - this is how you create modern websites without limits.', '2026-03-08 14:20:00'),
(26, 'carousel_8_description', 'de', 'Modular, flexibel und leistungsstark - so gestaltest du moderne Websites ohne Grenzen.', '2026-03-08 14:20:00'),
(27, 'carousel_8_description', 'it', 'Modulare, flessibile e potente - cosi crei siti web moderni senza limiti.', '2026-03-08 14:20:00');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `plugins_carousel_settings`
--

CREATE TABLE `plugins_carousel_settings` (
  `carouselID` int(11) NOT NULL,
  `carousel_height` varchar(255) NOT NULL DEFAULT '0',
  `parallax_height` varchar(255) NOT NULL DEFAULT '0',
  `sticky_height` varchar(255) NOT NULL DEFAULT '0',
  `agency_height` varchar(255) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Daten für Tabelle `plugins_carousel_settings`
--

INSERT INTO `plugins_carousel_settings` (`carouselID`, `carousel_height`, `parallax_height`, `sticky_height`, `agency_height`) VALUES
(1, '75vh', '75vh', '75vh', '75vh');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `plugins_discord`
--

CREATE TABLE `plugins_discord` (
  `name` varchar(100) NOT NULL,
  `value` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `plugins_discord`
--

INSERT INTO `plugins_discord` (`name`, `value`) VALUES
('server_id', '1438961150028025929');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `plugins_downloads`
--

CREATE TABLE `plugins_downloads` (
  `id` int(11) NOT NULL,
  `categoryID` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `file` varchar(255) NOT NULL,
  `downloads` int(11) DEFAULT 0,
  `access_roles` text DEFAULT NULL,
  `uploaded_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `plugins_downloads`
--

INSERT INTO `plugins_downloads` (`id`, `categoryID`, `title`, `description`, `file`, `downloads`, `access_roles`, `uploaded_at`, `updated_at`) VALUES
(1, 1, 'Erster Test', 'dwdwd', 'dl_nexpell-installer-anpassung_69ab250d48945.zip', 0, '[\"Admin\",\"Registrierter Benutzer\"]', '2026-03-06 20:03:41', '2026-03-06 20:03:41'),
(2, 2, 'Test Thema', 'c dbdbdb', 'dl_nexpell-installer-anpassung_69ab266f063ca.zip', 0, '[\"Admin\",\"Registrierter Benutzer\"]', '2026-03-06 20:09:35', '2026-03-06 20:09:35');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `plugins_downloads_categories`
--

CREATE TABLE `plugins_downloads_categories` (
  `categoryID` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `plugins_downloads_categories`
--

INSERT INTO `plugins_downloads_categories` (`categoryID`, `title`, `description`) VALUES
(1, 'Nützliche Tools', 'ddd'),
(2, 'Nützliche Tools 2', 'sfewgeg');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `plugins_downloads_logs`
--

CREATE TABLE `plugins_downloads_logs` (
  `logID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `fileID` int(11) NOT NULL,
  `downloaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `plugins_footer`
--

CREATE TABLE `plugins_footer` (
  `id` int(10) UNSIGNED NOT NULL,
  `row_type` enum('category','link','footer_text','footer_template') NOT NULL DEFAULT 'link',
  `category_key` varchar(64) NOT NULL DEFAULT '',
  `section_title` varchar(255) NOT NULL DEFAULT 'Navigation',
  `section_sort` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `link_sort` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `footer_link_name` varchar(255) NOT NULL DEFAULT '',
  `footer_link_url` varchar(255) NOT NULL DEFAULT '',
  `new_tab` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `plugins_footer`
--

INSERT INTO `plugins_footer` (`id`, `row_type`, `category_key`, `section_title`, `section_sort`, `link_sort`, `footer_link_name`, `footer_link_url`, `new_tab`) VALUES
(1, 'category', '97cef6e0a43f670b6b06577a5530d1a4', 'Legal', 2, 1, '', '', 0),
(2, 'category', '846495f9ceed11accf8879f555936a7d', 'Navigation', 1, 1, '', '', 0),
(3, 'link', '97cef6e0a43f670b6b06577a5530d1a4', 'Rechtliches', 2, 1, '', 'index.php?site=privacy_policy', 0),
(4, 'link', '97cef6e0a43f670b6b06577a5530d1a4', 'Rechtliches', 2, 2, '', 'index.php?site=imprint', 0),
(5, 'link', '97cef6e0a43f670b6b06577a5530d1a4', 'Rechtliches', 2, 3, '', 'index.php?site=terms_of_service', 0),
(6, 'link', '846495f9ceed11accf8879f555936a7d', 'Navigation', 1, 1, '', 'index.php?site=contact', 0),
(7, 'footer_text', '', 'footer_description', 0, 0, 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam.', '', 0),
(8, 'footer_template', '', 'footer_template', 1, 1, 'modern', '', 0);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `plugins_footer_lang`
--

CREATE TABLE `plugins_footer_lang` (
  `id` int(11) NOT NULL,
  `content_key` varchar(80) NOT NULL,
  `language` char(2) NOT NULL,
  `content` mediumtext NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `plugins_footer_lang`
--

INSERT INTO `plugins_footer_lang` (`id`, `content_key`, `language`, `content`, `updated_at`) VALUES
(1, 'link_name_1', 'de', 'Impressum', '2026-03-02 19:37:01'),
(2, 'link_name_1', 'en', 'Imprint', '2026-03-02 19:37:01'),
(3, 'link_name_1', 'it', 'Impronta Editoriale', '2026-03-02 19:37:01'),
(4, 'link_name_2', 'de', 'Datenschutz', '2026-03-02 19:37:01'),
(5, 'link_name_2', 'en', 'Privacy Policy', '2026-03-02 19:37:01'),
(6, 'link_name_2', 'it', 'Informativa sulla Privacy', '2026-03-02 19:37:01'),
(7, 'link_name_3', 'de', 'Datenschutz', '2026-03-15 14:11:36'),
(8, 'link_name_3', 'en', 'Data protection', '2026-03-15 14:11:36'),
(9, 'link_name_3', 'it', 'Protezione dei dati', '2026-03-15 14:11:36'),
(13, 'link_name_4', 'de', 'Impressum', '2026-03-15 14:11:36'),
(14, 'link_name_4', 'en', 'Imprint', '2026-03-15 14:11:36'),
(15, 'link_name_4', 'it', 'Impronta Editoriale', '2026-03-15 14:11:36'),
(16, 'link_name_6', 'de', 'Kontakt', '2026-03-15 14:11:36'),
(17, 'link_name_6', 'en', 'Contact', '2026-03-15 14:11:36'),
(18, 'link_name_6', 'it', 'Contatti', '2026-03-15 14:11:36'),
(19, 'link_name_5', 'de', 'Nutzungsbedinungen', '2026-03-15 14:11:36'),
(20, 'link_name_5', 'en', 'Terms of Use', '2026-03-15 14:11:36'),
(21, 'link_name_5', 'it', 'Termini di utilizzo', '2026-03-15 14:11:36'),
(22, 'footer_text', 'de', 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam.', '2026-03-18 22:12:31'),
(23, 'footer_text', 'en', 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam.', '2026-03-18 22:12:31'),
(24, 'footer_text', 'it', 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam.', '2026-03-18 22:12:31'),
(25, 'cat_title_846495f9ceed11accf8879f555936a7d', 'de', 'Navigation', '2026-03-15 14:11:36'),
(26, 'cat_title_97cef6e0a43f670b6b06577a5530d1a4', 'de', 'Rechtliches', '2026-03-15 14:11:36'),
(33, 'cat_title_97cef6e0a43f670b6b06577a5530d1a4', 'en', 'Legal', '2026-03-02 17:38:33'),
(35, 'cat_title_97cef6e0a43f670b6b06577a5530d1a4', 'it', 'Legal', '2026-03-02 17:38:33');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `plugins_forum_boards`
--

CREATE TABLE `plugins_forum_boards` (
  `boardID` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `position` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `plugins_forum_boards`
--

INSERT INTO `plugins_forum_boards` (`boardID`, `title`, `description`, `position`) VALUES
(1, 'Allgemeine Diskussionen', 'Alles rund um die Community', 1),
(2, 'Technik & Support', 'Hardware, Software & Hilfe', 2),
(3, 'Community Talk', 'Off-Topic & Vorstellung', 3);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `plugins_forum_categories`
--

CREATE TABLE `plugins_forum_categories` (
  `catID` int(10) UNSIGNED NOT NULL,
  `boardID` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `position` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `plugins_forum_categories`
--

INSERT INTO `plugins_forum_categories` (`catID`, `boardID`, `title`, `description`, `position`) VALUES
(1, 1, 'Allgemeines', 'Diskussionen rund um allgemeine Themen', 1),
(2, 2, 'Support', 'Hilfe, Fragen und technische Probleme', 2),
(3, 3, 'Off-Topic', 'Alles was sonst nirgends reinpasst', 3);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `plugins_forum_permissions_board`
--

CREATE TABLE `plugins_forum_permissions_board` (
  `id` int(10) UNSIGNED NOT NULL,
  `boardID` int(10) UNSIGNED NOT NULL,
  `role_id` int(10) UNSIGNED NOT NULL,
  `can_view` tinyint(1) DEFAULT NULL,
  `can_read` tinyint(1) DEFAULT NULL,
  `can_post` tinyint(1) DEFAULT NULL,
  `can_reply` tinyint(1) DEFAULT NULL,
  `can_edit` tinyint(1) DEFAULT NULL,
  `can_delete` tinyint(1) DEFAULT NULL,
  `is_mod` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `plugins_forum_permissions_board`
--

INSERT INTO `plugins_forum_permissions_board` (`id`, `boardID`, `role_id`, `can_view`, `can_read`, `can_post`, `can_reply`, `can_edit`, `can_delete`, `is_mod`) VALUES
(1, 1, 1, 1, 1, 1, 1, 1, 1, 1),
(2, 1, 7, 1, 1, 1, 1, NULL, NULL, 1),
(3, 1, 4, 1, 1, 1, 1, NULL, NULL, NULL),
(4, 1, 5, 1, 1, 1, 1, NULL, NULL, NULL),
(5, 1, 6, 1, 1, 1, 1, NULL, NULL, NULL),
(6, 1, 8, 1, 1, 1, 1, NULL, NULL, NULL),
(7, 1, 9, 1, 1, 1, 1, NULL, NULL, NULL),
(8, 1, 10, 1, 1, 1, 1, NULL, NULL, NULL),
(9, 1, 12, 1, 1, 1, 1, NULL, NULL, NULL),
(10, 1, 13, 1, 1, 1, 1, NULL, NULL, NULL),
(11, 1, 14, 1, 1, 1, 1, NULL, NULL, NULL),
(12, 1, 16, 1, 1, 1, 1, NULL, NULL, NULL),
(13, 1, 11, 1, 1, NULL, NULL, NULL, NULL, NULL),
(14, 1, 15, 1, 1, NULL, NULL, NULL, NULL, NULL),
(15, 2, 1, 1, 1, 1, 1, 1, 1, 1),
(16, 2, 7, 1, 1, 1, 1, NULL, NULL, 1),
(17, 2, 4, 1, 1, 1, 1, NULL, NULL, NULL),
(18, 2, 5, 1, 1, 1, 1, NULL, NULL, NULL),
(19, 2, 6, 1, 1, 1, 1, NULL, NULL, NULL),
(20, 2, 8, 1, 1, 1, 1, NULL, NULL, NULL),
(21, 2, 9, 1, 1, 1, 1, NULL, NULL, NULL),
(22, 2, 10, 1, 1, 1, 1, NULL, NULL, NULL),
(23, 2, 12, 1, 1, 1, 1, NULL, NULL, NULL),
(24, 2, 13, 1, 1, 1, 1, NULL, NULL, NULL),
(25, 2, 14, 1, 1, 1, 1, NULL, NULL, NULL),
(26, 2, 16, 1, 1, 1, 1, NULL, NULL, NULL),
(27, 2, 11, 1, 1, NULL, NULL, NULL, NULL, NULL),
(28, 2, 15, 1, 1, NULL, NULL, NULL, NULL, NULL),
(29, 3, 1, 1, 1, 1, 1, 1, 1, 1),
(30, 3, 7, 1, 1, 1, 1, NULL, NULL, 1),
(31, 3, 4, 1, 1, 1, 1, NULL, NULL, NULL),
(32, 3, 5, 1, 1, 1, 1, NULL, NULL, NULL),
(33, 3, 6, 1, 1, 1, 1, NULL, NULL, NULL),
(34, 3, 8, 1, 1, 1, 1, NULL, NULL, NULL),
(35, 3, 9, 1, 1, 1, 1, NULL, NULL, NULL),
(36, 3, 10, 1, 1, 1, 1, NULL, NULL, NULL),
(37, 3, 12, 1, 1, 1, 1, NULL, NULL, NULL),
(38, 3, 13, 1, 1, 1, 1, NULL, NULL, NULL),
(39, 3, 14, 1, 1, 1, 1, NULL, NULL, NULL),
(40, 3, 16, 1, 1, 1, 1, NULL, NULL, NULL),
(41, 3, 11, 1, 1, NULL, NULL, NULL, NULL, NULL),
(42, 3, 15, 1, 1, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `plugins_forum_permissions_categories`
--

CREATE TABLE `plugins_forum_permissions_categories` (
  `id` int(10) UNSIGNED NOT NULL,
  `catID` int(10) UNSIGNED NOT NULL,
  `role_id` int(10) UNSIGNED NOT NULL,
  `can_view` tinyint(1) DEFAULT NULL,
  `can_read` tinyint(1) DEFAULT NULL,
  `can_post` tinyint(1) DEFAULT NULL,
  `can_reply` tinyint(1) DEFAULT NULL,
  `can_edit` tinyint(1) DEFAULT NULL,
  `can_delete` tinyint(1) DEFAULT NULL,
  `is_mod` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `plugins_forum_permissions_categories`
--

INSERT INTO `plugins_forum_permissions_categories` (`id`, `catID`, `role_id`, `can_view`, `can_read`, `can_post`, `can_reply`, `can_edit`, `can_delete`, `is_mod`) VALUES
(1, 1, 1, 1, 1, 1, 1, 1, 1, 1),
(2, 1, 7, 1, 1, 1, 1, NULL, NULL, 1),
(3, 1, 4, 1, 1, 1, 1, NULL, NULL, NULL),
(4, 1, 5, 1, 1, 1, 1, NULL, NULL, NULL),
(5, 1, 6, 1, 1, 1, 1, NULL, NULL, NULL),
(6, 1, 8, 1, 1, 1, 1, NULL, NULL, NULL),
(7, 1, 9, 1, 1, 1, 1, NULL, NULL, NULL),
(8, 1, 10, 1, 1, 1, 1, NULL, NULL, NULL),
(9, 1, 12, 1, 1, 1, 1, NULL, NULL, NULL),
(10, 1, 13, 1, 1, 1, 1, NULL, NULL, NULL),
(11, 1, 14, 1, 1, 1, 1, NULL, NULL, NULL),
(12, 1, 16, 1, 1, 1, 1, NULL, NULL, NULL),
(13, 1, 11, 1, 1, NULL, NULL, NULL, NULL, NULL),
(14, 1, 15, 1, 1, NULL, NULL, NULL, NULL, NULL),
(15, 2, 1, 1, 1, 1, 1, 1, 1, 1),
(16, 2, 7, 1, 1, 1, 1, NULL, NULL, 1),
(17, 2, 4, 1, 1, 1, 1, NULL, NULL, NULL),
(18, 2, 5, 1, 1, 1, 1, NULL, NULL, NULL),
(19, 2, 6, 1, 1, 1, 1, NULL, NULL, NULL),
(20, 2, 8, 1, 1, 1, 1, NULL, NULL, NULL),
(21, 2, 9, 1, 1, 1, 1, NULL, NULL, NULL),
(22, 2, 10, 1, 1, 1, 1, NULL, NULL, NULL),
(23, 2, 12, 1, 1, 1, 1, NULL, NULL, NULL),
(24, 2, 13, 1, 1, 1, 1, NULL, NULL, NULL),
(25, 2, 14, 1, 1, 1, 1, NULL, NULL, NULL),
(26, 2, 16, 1, 1, 1, 1, NULL, NULL, NULL),
(27, 2, 11, 1, 1, NULL, NULL, NULL, NULL, NULL),
(28, 2, 15, 1, 1, NULL, NULL, NULL, NULL, NULL),
(29, 3, 1, 1, 1, 1, 1, 1, 1, 1),
(30, 3, 7, 1, 1, 1, 1, NULL, NULL, 1),
(31, 3, 4, 1, 1, 1, 1, NULL, NULL, NULL),
(32, 3, 5, 1, 1, 1, 1, NULL, NULL, NULL),
(33, 3, 6, 1, 1, 1, 1, NULL, NULL, NULL),
(34, 3, 8, 1, 1, 1, 1, NULL, NULL, NULL),
(35, 3, 9, 1, 1, 1, 1, NULL, NULL, NULL),
(36, 3, 10, 1, 1, 1, 1, NULL, NULL, NULL),
(37, 3, 12, 1, 1, 1, 1, NULL, NULL, NULL),
(38, 3, 13, 1, 1, 1, 1, NULL, NULL, NULL),
(39, 3, 14, 1, 1, 1, 1, NULL, NULL, NULL),
(40, 3, 16, 1, 1, 1, 1, NULL, NULL, NULL),
(41, 3, 11, 1, 1, NULL, NULL, NULL, NULL, NULL),
(42, 3, 15, 1, 1, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `plugins_forum_permissions_threads`
--

CREATE TABLE `plugins_forum_permissions_threads` (
  `id` int(10) UNSIGNED NOT NULL,
  `threadID` int(10) UNSIGNED NOT NULL,
  `role_id` int(10) UNSIGNED NOT NULL,
  `can_view` tinyint(1) DEFAULT NULL,
  `can_read` tinyint(1) DEFAULT NULL,
  `can_post` tinyint(1) DEFAULT NULL,
  `can_reply` tinyint(1) DEFAULT NULL,
  `can_edit` tinyint(1) DEFAULT NULL,
  `can_delete` tinyint(1) DEFAULT NULL,
  `is_mod` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `plugins_forum_posts`
--

CREATE TABLE `plugins_forum_posts` (
  `postID` int(10) UNSIGNED NOT NULL,
  `threadID` int(10) UNSIGNED NOT NULL,
  `userID` int(10) UNSIGNED NOT NULL,
  `content` text NOT NULL,
  `created_at` int(11) NOT NULL,
  `edited_at` int(11) DEFAULT NULL,
  `edited_by` int(11) DEFAULT NULL,
  `is_deleted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `plugins_forum_posts`
--

INSERT INTO `plugins_forum_posts` (`postID`, `threadID`, `userID`, `content`, `created_at`, `edited_at`, `edited_by`, `is_deleted`) VALUES
(1, 1, 1, 'qsqs', 1773259101, NULL, NULL, 0),
(2, 1, 1, 'qdqdq', 1773259109, NULL, NULL, 0),
(3, 1, 1, 'ff<img src=\"/images/uploads/nx_editor/nx_20260311_210702_7a59bf2d5c97.jpg\" alt=\"\">', 1773259626, 1773260513, NULL, 0);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `plugins_forum_post_likes`
--

CREATE TABLE `plugins_forum_post_likes` (
  `postID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `plugins_forum_read`
--

CREATE TABLE `plugins_forum_read` (
  `userID` int(11) NOT NULL,
  `threadID` int(11) NOT NULL,
  `last_read_at` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `plugins_forum_read`
--

INSERT INTO `plugins_forum_read` (`userID`, `threadID`, `last_read_at`) VALUES
(1, 1, 1774189921);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `plugins_forum_threads`
--

CREATE TABLE `plugins_forum_threads` (
  `threadID` int(10) UNSIGNED NOT NULL,
  `catID` int(10) UNSIGNED NOT NULL,
  `userID` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `views` int(11) DEFAULT 0,
  `is_locked` tinyint(1) DEFAULT 0,
  `is_pinned` tinyint(1) NOT NULL DEFAULT 0,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `last_post_at` int(11) NOT NULL DEFAULT 0,
  `last_post_userID` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `plugins_forum_threads`
--

INSERT INTO `plugins_forum_threads` (`threadID`, `catID`, `userID`, `title`, `created_at`, `updated_at`, `views`, `is_locked`, `is_pinned`, `is_deleted`, `last_post_at`, `last_post_userID`) VALUES
(1, 1, 1, 'Erster Test', 1773259101, 1773259101, 0, 0, 0, 0, 1773259101, 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `plugins_forum_uploaded_images`
--

CREATE TABLE `plugins_forum_uploaded_images` (
  `id` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `created_at` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `plugins_gallery`
--

CREATE TABLE `plugins_gallery` (
  `id` int(10) UNSIGNED NOT NULL,
  `filename` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL DEFAULT '',
  `caption` text DEFAULT NULL,
  `alt_text` varchar(255) NOT NULL DEFAULT '',
  `tags` varchar(255) NOT NULL DEFAULT '',
  `photographer` varchar(255) NOT NULL DEFAULT '',
  `width` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `height` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `class` enum('wide','tall','big','') DEFAULT '',
  `upload_date` datetime NOT NULL DEFAULT current_timestamp(),
  `position` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `category_id` int(10) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `plugins_gallery`
--

INSERT INTO `plugins_gallery` (`id`, `filename`, `title`, `caption`, `alt_text`, `tags`, `photographer`, `width`, `height`, `class`, `upload_date`, `position`, `category_id`) VALUES
(1, 'img_68839134a01b4.jpg', '', NULL, '', '', '', 0, 0, '', '2026-03-09 17:44:50', 2, 2),
(2, 'img_688391421701b.jpg', '', NULL, '', '', '', 0, 0, 'wide', '2026-03-09 17:44:50', 3, 2),
(3, 'img_6883914ca02e4.jpg', '', NULL, '', '', '', 0, 0, 'tall', '2026-03-09 17:44:50', 4, 1),
(4, 'img_688391578247d.jpg', '', NULL, '', '', '', 0, 0, 'wide', '2026-03-09 17:44:50', 5, 2),
(5, 'img_68839167eadb7.jpg', '', NULL, '', '', '', 0, 0, '', '2026-03-09 17:44:50', 6, 1),
(6, 'img_688391793db05.jpg', '', NULL, '', '', '', 0, 0, 'tall', '2026-03-09 17:44:50', 7, 1),
(7, 'img_6883918321c6a.jpg', '', NULL, '', '', '', 0, 0, '', '2026-03-09 17:44:50', 8, 1),
(8, 'img_6883918f85626.jpg', '', NULL, '', '', '', 0, 0, 'big', '2026-03-09 17:44:50', 9, 2),
(9, 'img_6883919ad6c13.jpg', '', NULL, '', '', '', 0, 0, '', '2026-03-09 17:44:50', 10, 2),
(10, 'img_688391a5ecfa1.jpg', 'Kopfhörer', 'Beschreibung vom Bild Kopfhörer aaaaaaa bbbb ccccc', 'Kopfhörer', 'Kopfhörer Tags', 'T-seven', 0, 0, '', '2026-03-09 17:44:50', 11, 1),
(11, 'img_688391b3c986c.jpg', '', NULL, '', '', '', 0, 0, 'wide', '2026-03-09 17:44:50', 12, 1),
(12, 'img_688391be98734.jpg', '', NULL, '', '', '', 0, 0, '', '2026-03-09 17:44:50', 1, 1),
(13, 'img_69b6f8553ad469.24079253.jpg', 'Erster Test', '', '', '', '', 300, 450, '', '2026-03-15 19:20:05', 13, 2);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `plugins_gallery_categories`
--

CREATE TABLE `plugins_gallery_categories` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `plugins_gallery_categories`
--

INSERT INTO `plugins_gallery_categories` (`id`, `name`) VALUES
(1, 'Elektro'),
(2, 'Design');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `plugins_gametracker_servers`
--

CREATE TABLE `plugins_gametracker_servers` (
  `id` int(11) NOT NULL,
  `ip` varchar(100) NOT NULL,
  `port` int(11) NOT NULL,
  `query_port` int(11) DEFAULT NULL,
  `game` varchar(50) NOT NULL,
  `game_pic` varchar(255) NOT NULL,
  `active` tinyint(1) DEFAULT 1,
  `sort_order` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `plugins_gametracker_servers`
--

INSERT INTO `plugins_gametracker_servers` (`id`, `ip`, `port`, `query_port`, `game`, `game_pic`, `active`, `sort_order`) VALUES
(1, '85.14.192.114', 28960, NULL, 'coduo', 'uo', 1, 0);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `plugins_joinus_applications`
--

CREATE TABLE `plugins_joinus_applications` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `role` int(11) NOT NULL DEFAULT 0,
  `role_custom` varchar(255) DEFAULT NULL,
  `squad_id` int(11) DEFAULT NULL,
  `type` varchar(50) DEFAULT 'team',
  `status` enum('new','review','accepted','rejected') DEFAULT 'new',
  `admin_note` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `last_status` varchar(20) DEFAULT 'new',
  `processed_at` datetime DEFAULT NULL,
  `mail_sent_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `plugins_joinus_applications`
--

INSERT INTO `plugins_joinus_applications` (`id`, `name`, `email`, `message`, `role`, `role_custom`, `squad_id`, `type`, `status`, `admin_note`, `created_at`, `last_status`, `processed_at`, `mail_sent_at`) VALUES
(1, 'tom', 'info@nexpell.de', 'testnachricht', 3, '', 0, 'partner', 'accepted', '', '2026-03-09 22:03:02', 'rejected', '2026-03-09 22:06:24', '2026-03-09 22:06:24');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `plugins_joinus_roles`
--

CREATE TABLE `plugins_joinus_roles` (
  `id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `is_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `plugins_joinus_roles`
--

INSERT INTO `plugins_joinus_roles` (`id`, `role_id`, `is_enabled`, `created_at`, `updated_at`) VALUES
(1, 2, 1, '2026-03-09 20:59:50', NULL),
(2, 3, 1, '2026-03-09 20:59:50', NULL),
(3, 4, 1, '2026-03-09 20:59:50', NULL),
(4, 5, 1, '2026-03-09 20:59:50', NULL),
(5, 6, 1, '2026-03-09 20:59:50', NULL),
(6, 7, 1, '2026-03-09 20:59:50', NULL),
(7, 8, 1, '2026-03-09 20:59:50', NULL),
(8, 9, 1, '2026-03-09 20:59:50', NULL),
(9, 10, 1, '2026-03-09 20:59:50', NULL),
(10, 11, 1, '2026-03-09 20:59:50', NULL),
(11, 12, 1, '2026-03-09 20:59:50', NULL),
(12, 13, 1, '2026-03-09 20:59:50', NULL),
(13, 14, 1, '2026-03-09 20:59:50', NULL),
(14, 15, 1, '2026-03-09 20:59:50', NULL),
(15, 16, 1, '2026-03-09 20:59:50', NULL);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `plugins_joinus_squads`
--

CREATE TABLE `plugins_joinus_squads` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `is_enabled` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `plugins_joinus_squads`
--

INSERT INTO `plugins_joinus_squads` (`id`, `name`, `is_enabled`) VALUES
(1, 'Alpha Squad (CS2)', 1),
(2, 'Bravo Squad (Valorant)', 1),
(3, 'Community Squad', 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `plugins_joinus_types`
--

CREATE TABLE `plugins_joinus_types` (
  `id` int(11) NOT NULL,
  `type_key` varchar(32) NOT NULL,
  `label` varchar(255) NOT NULL,
  `is_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `plugins_joinus_types`
--

INSERT INTO `plugins_joinus_types` (`id`, `type_key`, `label`, `is_enabled`, `sort_order`) VALUES
(1, 'team', 'Team', 1, 1),
(2, 'partner', 'Partner', 1, 2),
(3, 'squad', 'Squad', 1, 3);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `plugins_links`
--

CREATE TABLE `plugins_links` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `url` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `target` varchar(10) DEFAULT '_blank',
  `visible` tinyint(1) DEFAULT 1,
  `userID` int(11) NOT NULL DEFAULT 0,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `plugins_links`
--

INSERT INTO `plugins_links` (`id`, `title`, `url`, `description`, `category_id`, `image`, `target`, `visible`, `userID`, `updated_at`) VALUES
(1, 'Linus Tech Tips', 'https://www.youtube.com/user/LinusTechTips', 'Technik-Videos rund ums Thema PC', 2, 'includes/plugins/links/images/linkimg_linus-tech-tips_1763315541.jpg', '_blank', 1, 1, '2025-06-01 07:46:22'),
(2, 'PHP Offizielle Webseite', 'https://php.net', 'Offizielle PHP Webseite mit Doku und Downloads', 3, 'includes/plugins/links/images/linkimg_php-offizielle-webseite_1763315522.png', '_blank', 1, 1, '2025-06-01 07:46:22'),
(3, 'GitHub', 'https://github.com', 'Hosting für Softwareprojekte mit Git', 3, 'includes/plugins/links/images/linkimg_github_1763315516.png', '_blank', 1, 1, '2025-06-01 07:46:22'),
(4, 'callofduty', 'https://www.callofduty.com', '', 4, 'includes/plugins/links/images/linkimg_callofduty_1763316924.webp', '_blank', 1, 1, '2025-06-01 07:46:22'),
(5, 'all-inkl', 'https://all-inkl.com', 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.', 3, 'includes/plugins/links/images/linkimg_all-inkl_1763315378.svg', '_blank', 1, 1, '2025-06-01 07:46:22'),
(6, 'nexpell', 'https://www.nexpell.de', 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.', 1, 'includes/plugins/links/images/linkimg_nexpell_1763315534.jpg', '_blank', 1, 1, '2025-06-01 07:46:22'),
(7, 'werstreamt.es', 'https://www.werstreamt.es/', '', 3, 'includes/plugins/links/images/linkimg_werstreamt-es_1763315528.png', '_blank', 1, 1, '2025-06-01 07:46:22'),
(8, 'Miley Cyrus', 'https://www.youtube.com/watch?v=CXBFU97X61I&list=RDMMCXBFU97X61I&start_radio=1', 'Miley Cyrus - End of the World', 2, 'includes/plugins/links/images/linkimg_miley-cyrus_1763315550.jpg', '_blank', 1, 1, '2025-06-01 07:46:22');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `plugins_links_categories`
--

CREATE TABLE `plugins_links_categories` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `icon` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `plugins_links_categories`
--

INSERT INTO `plugins_links_categories` (`id`, `title`, `icon`) VALUES
(1, 'Webseiten', 'bi bi-globe'),
(2, 'YouTube-Kanäle', 'bi bi-youtube'),
(3, 'Tools & Dienste', 'bi bi-tools'),
(4, 'Gaming', 'bi bi-controller'),
(5, 'Lernen & Wissen', 'bi bi-book');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `plugins_links_settings`
--

CREATE TABLE `plugins_links_settings` (
  `linkssetID` int(11) NOT NULL,
  `links` int(11) NOT NULL,
  `linkchars` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Daten für Tabelle `plugins_links_settings`
--

INSERT INTO `plugins_links_settings` (`linkssetID`, `links`, `linkchars`) VALUES
(1, 4, 300);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `plugins_messages`
--

CREATE TABLE `plugins_messages` (
  `id` int(11) NOT NULL,
  `sender_id` varchar(255) NOT NULL,
  `receiver_id` varchar(255) NOT NULL,
  `text` text NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_read` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `plugins_messages`
--

INSERT INTO `plugins_messages` (`id`, `sender_id`, `receiver_id`, `text`, `image_url`, `timestamp`, `is_read`) VALUES
(1, '1', '2', 'test1', NULL, '2026-03-08 13:37:32', 1),
(2, '2', '1', 'zurÃ¼ck', NULL, '2026-03-08 13:38:08', 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `plugins_news`
--

CREATE TABLE `plugins_news` (
  `id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL DEFAULT '',
  `slug` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `link` varchar(255) NOT NULL DEFAULT '',
  `link_name` varchar(255) NOT NULL DEFAULT '',
  `banner_image` varchar(255) NOT NULL DEFAULT '',
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
  `publish_at` datetime DEFAULT NULL,
  `userID` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `topnews_is_active` tinyint(1) NOT NULL DEFAULT 0,
  `views` int(11) NOT NULL DEFAULT 0,
  `allow_comments` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `plugins_news`
--

INSERT INTO `plugins_news` (`id`, `category_id`, `title`, `slug`, `content`, `link`, `link_name`, `banner_image`, `sort_order`, `updated_at`, `publish_at`, `userID`, `is_active`, `topnews_is_active`, `views`, `allow_comments`) VALUES
(1, 1, 'Willkommen im News-Plugin', 'willkommen-im-news-plugin', 'Das ist ein automatisch angelegter Beispielartikel.\r\n\r\nDu kannst ihn im Adminbereich bearbeiten oder löschen.', 'index.php?site=joinus', 'Joinus', '', 1, '2026-03-08 14:40:12', '2026-03-08 14:40:00', 1, 1, 0, 22, 0),
(2, 2, 'Nexpell 1.0.3.1 – Neues ACL-Forum &amp; System-Upgrade', 'nexpell-1-0-3-1-neues-acl-forum-system-upgrade', 'wfwfwfw', '', '', '', 2, '2026-03-08 15:19:36', NULL, 1, 1, 0, 21, 0),
(3, 1, '1', '1', 'fafaf', '', '', '', 0, '2026-03-15 19:42:16', NULL, 1, 1, 0, 0, 0),
(4, 2, '2', '2', 'aafaf', '', '', '', 0, '2026-03-15 19:42:28', NULL, 1, 1, 0, 0, 0);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `plugins_news_categories`
--

CREATE TABLE `plugins_news_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT '',
  `slug` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `plugins_news_categories`
--

INSERT INTO `plugins_news_categories` (`id`, `name`, `slug`, `description`, `image`, `sort_order`) VALUES
(1, 'Allgemein', 'allgemein', 'Standard-Rubrik für News-Beiträge.', '1772977236_3.png', 0),
(2, 'Themes', 'themes', 'afafaf', '1772979305_2-2.jpg', 0);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `plugins_news_lang`
--

CREATE TABLE `plugins_news_lang` (
  `id` int(11) NOT NULL,
  `content_key` varchar(80) NOT NULL,
  `language` char(2) NOT NULL,
  `content` mediumtext NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `plugins_news_lang`
--

INSERT INTO `plugins_news_lang` (`id`, `content_key`, `language`, `content`, `updated_at`) VALUES
(1, 'news_1_title', 'de', 'Willkommen im News-Plugin', '2026-03-15 15:05:26'),
(2, 'news_1_content', 'de', 'Das ist ein automatisch angelegter Beispielartikel.\r\n\r\nDu kannst ihn im Adminbereich bearbeiten oder löschen.', '2026-03-15 15:05:26'),
(3, 'news_1_link_name', 'de', 'Joinus', '2026-03-15 15:05:26'),
(4, 'news_1_title', 'en', 'Willkommen im News-Plugin', '2026-03-15 15:05:26'),
(5, 'news_1_content', 'en', 'Das ist ein automatisch angelegter Beispielartikel.\r\n\r\nDu kannst ihn im Adminbereich bearbeiten oder löschen.', '2026-03-15 15:05:26'),
(6, 'news_1_link_name', 'en', 'Joinus', '2026-03-15 15:05:26'),
(7, 'news_1_title', 'it', 'Willkommen im News-Plugin', '2026-03-15 15:05:26'),
(8, 'news_1_content', 'it', 'Das ist ein automatisch angelegter Beispielartikel.\r\n\r\nDu kannst ihn im Adminbereich bearbeiten oder löschen.', '2026-03-15 15:05:26'),
(9, 'news_1_link_name', 'it', 'Joinus', '2026-03-15 15:05:26'),
(10, 'news_2_title', 'en', 'Nexpell 1.0.3.1 – Neues ACL-Forum &amp; System-Upgrade', '2026-03-15 15:05:26'),
(11, 'news_2_title', 'de', 'Nexpell 1.0.3.1 – Neues ACL-Forum &amp; System-Upgrade', '2026-03-15 15:05:26'),
(12, 'news_2_title', 'it', 'Nexpell 1.0.3.1 – Neues ACL-Forum &amp; System-Upgrade', '2026-03-15 15:05:26'),
(13, 'news_2_content', 'en', 'wfwfwfw', '2026-03-15 15:05:26'),
(14, 'news_2_content', 'de', 'wfwfwfw', '2026-03-15 15:05:26'),
(15, 'news_2_content', 'it', 'wfwfwfw', '2026-03-15 15:05:26'),
(16, 'news_2_link_name', 'en', '', '2026-03-15 15:05:26'),
(17, 'news_2_link_name', 'de', '', '2026-03-15 15:05:26'),
(18, 'news_2_link_name', 'it', '', '2026-03-15 15:05:26'),
(55, 'news_3_title', 'en', '', '2026-03-15 19:42:16'),
(56, 'news_3_title', 'de', '1', '2026-03-15 19:42:16'),
(57, 'news_3_title', 'it', '', '2026-03-15 19:42:16'),
(58, 'news_3_content', 'en', '', '2026-03-15 19:42:16'),
(59, 'news_3_content', 'de', 'fafaf', '2026-03-15 19:42:16'),
(60, 'news_3_content', 'it', '', '2026-03-15 19:42:16'),
(61, 'news_3_link_name', 'en', '', '2026-03-15 19:42:16'),
(62, 'news_3_link_name', 'de', '', '2026-03-15 19:42:16'),
(63, 'news_3_link_name', 'it', '', '2026-03-15 19:42:16'),
(64, 'news_4_title', 'en', '', '2026-03-15 19:42:28'),
(65, 'news_4_title', 'de', '2', '2026-03-15 19:42:28'),
(66, 'news_4_title', 'it', '', '2026-03-15 19:42:28'),
(67, 'news_4_content', 'en', '', '2026-03-15 19:42:28'),
(68, 'news_4_content', 'de', 'aafaf', '2026-03-15 19:42:28'),
(69, 'news_4_content', 'it', '', '2026-03-15 19:42:28'),
(70, 'news_4_link_name', 'en', '', '2026-03-15 19:42:28'),
(71, 'news_4_link_name', 'de', '', '2026-03-15 19:42:28'),
(72, 'news_4_link_name', 'it', '', '2026-03-15 19:42:28');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `plugins_partners`
--

CREATE TABLE `plugins_partners` (
  `id` int(11) NOT NULL,
  `content_key` varchar(80) NOT NULL,
  `language` char(2) NOT NULL,
  `content` mediumtext NOT NULL,
  `slug` varchar(255) NOT NULL DEFAULT '',
  `logo` varchar(255) DEFAULT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
  `userID` int(11) NOT NULL DEFAULT 0,
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `plugins_partners`
--

INSERT INTO `plugins_partners` (`id`, `content_key`, `language`, `content`, `slug`, `logo`, `updated_at`, `userID`, `sort_order`, `is_active`) VALUES
(1, 'partner_1_name', 'de', 'Partner de', 'https://www.nexpell.de', 'partners_684593e67f7cc.png', '2026-03-05 20:38:28', 1, 1, 1),
(2, 'partner_1_name', 'en', 'Partner en', 'https://www.nexpell.de', 'partners_684593e67f7cc.png', '2026-03-05 20:38:20', 1, 1, 1),
(3, 'partner_1_name', 'it', 'Partner it', 'https://www.nexpell.de', 'partners_684593e67f7cc.png', '2026-03-05 20:38:08', 1, 1, 1),
(4, 'partner_1_description', 'de', 'Hallo. Ich bin ein kleiner Blindtext. Und zwar schon so lange ich denken kann.', 'https://www.nexpell.de', 'partners_684593e67f7cc.png', '2026-03-05 20:38:28', 1, 1, 1),
(5, 'partner_1_description', 'en', 'Hello. I am a small placeholder text and have been here for a long time.', 'https://www.nexpell.de', 'partners_684593e67f7cc.png', '2026-03-05 20:38:20', 1, 1, 1),
(6, 'partner_1_description', 'it', 'Ciao. Sono un piccolo testo segnaposto presente da molto tempo.', 'https://www.nexpell.de', 'partners_684593e67f7cc.png', '2026-03-05 20:38:08', 1, 1, 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `plugins_partners_settings`
--

CREATE TABLE `plugins_partners_settings` (
  `partnerssetID` int(11) NOT NULL,
  `partners` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Daten für Tabelle `plugins_partners_settings`
--

INSERT INTO `plugins_partners_settings` (`partnerssetID`, `partners`) VALUES
(1, 5);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `plugins_pricing_features`
--

CREATE TABLE `plugins_pricing_features` (
  `id` int(11) NOT NULL,
  `plan_id` int(11) NOT NULL,
  `feature_text` varchar(255) NOT NULL,
  `feature_text_de` varchar(255) NOT NULL DEFAULT '',
  `feature_text_en` varchar(255) NOT NULL DEFAULT '',
  `feature_text_it` varchar(255) NOT NULL DEFAULT '',
  `available` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `plugins_pricing_features`
--

INSERT INTO `plugins_pricing_features` (`id`, `plan_id`, `feature_text`, `feature_text_de`, `feature_text_en`, `feature_text_it`, `available`) VALUES
(1, 1, 'Aida dere', 'Aida dere', 'Aida dere', 'Aida dere', 1),
(2, 1, 'Nec feugiat nisl', 'Nec feugiat nisl', 'Nec feugiat nisl', 'Nec feugiat nisl', 1),
(3, 1, 'Nulla at volutpat dola', 'Nulla at volutpat dola', 'Nulla at volutpat dola', 'Nulla at volutpat dola', 1),
(4, 1, 'Pharetra massa', 'Pharetra massa', 'Pharetra massa', 'Pharetra massa', 0),
(5, 1, 'Massa ultricies mi', 'Massa ultricies mi', 'Massa ultricies mi', 'Massa ultricies mi', 0),
(6, 2, 'Aida dere', 'Aida dere', 'Aida dere', 'Aida dere', 1),
(7, 2, 'Nec feugiat nisl', 'Nec feugiat nisl', 'Nec feugiat nisl', 'Nec feugiat nisl', 1),
(8, 2, 'Nulla at volutpat dola', 'Nulla at volutpat dola', 'Nulla at volutpat dola', 'Nulla at volutpat dola', 1),
(9, 2, 'Pharetra massa', 'Pharetra massa', 'Pharetra massa', 'Pharetra massa', 1),
(10, 2, 'Massa ultricies mi', 'Massa ultricies mi', 'Massa ultricies mi', 'Massa ultricies mi', 0),
(11, 3, 'Aida dere', 'Aida dere', 'Aida dere', 'Aida dere', 1),
(12, 3, 'Nec feugiat nisl', 'Nec feugiat nisl', 'Nec feugiat nisl', 'Nec feugiat nisl', 1),
(13, 3, 'Nulla at volutpat dola', 'Nulla at volutpat dola', 'Nulla at volutpat dola', 'Nulla at volutpat dola', 1),
(14, 3, 'Pharetra massa', 'Pharetra massa', 'Pharetra massa', 'Pharetra massa', 1),
(15, 3, 'Massa ultricies mi', 'Massa ultricies mi', 'Massa ultricies mi', 'Massa ultricies mi', 1),
(16, 4, 'Aida dere', 'Aida dere', 'Aida dere', 'Aida dere', 1),
(17, 4, 'Nec feugiat nisl', 'Nec feugiat nisl', 'Nec feugiat nisl', 'Nec feugiat nisl', 1),
(18, 4, 'Nulla at volutpat dola', 'Nulla at volutpat dola', 'Nulla at volutpat dola', 'Nulla at volutpat dola', 1),
(19, 4, 'Pharetra massa', 'Pharetra massa', 'Pharetra massa', 'Pharetra massa', 1),
(20, 4, 'Massa ultricies mi', 'Massa ultricies mi', 'Massa ultricies mi', 'Massa ultricies mi', 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `plugins_pricing_plans`
--

CREATE TABLE `plugins_pricing_plans` (
  `id` int(11) NOT NULL,
  `title` varchar(100) DEFAULT NULL,
  `title_de` varchar(100) NOT NULL DEFAULT '',
  `title_en` varchar(100) NOT NULL DEFAULT '',
  `title_it` varchar(100) NOT NULL DEFAULT '',
  `target_url` varchar(255) NOT NULL DEFAULT '',
  `price` decimal(10,2) DEFAULT NULL,
  `price_unit` varchar(50) DEFAULT '/ month',
  `price_unit_de` varchar(50) NOT NULL DEFAULT '',
  `price_unit_en` varchar(50) NOT NULL DEFAULT '',
  `price_unit_it` varchar(50) NOT NULL DEFAULT '',
  `button_text_de` varchar(100) NOT NULL DEFAULT '',
  `button_text_en` varchar(100) NOT NULL DEFAULT '',
  `button_text_it` varchar(100) NOT NULL DEFAULT '',
  `is_featured` tinyint(1) DEFAULT 0,
  `is_advanced` tinyint(1) DEFAULT 0,
  `sort_order` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `plugins_pricing_plans`
--

INSERT INTO `plugins_pricing_plans` (`id`, `title`, `title_de`, `title_en`, `title_it`, `target_url`, `price`, `price_unit`, `price_unit_de`, `price_unit_en`, `price_unit_it`, `button_text_de`, `button_text_en`, `button_text_it`, `is_featured`, `is_advanced`, `sort_order`) VALUES
(1, 'Free', 'Free', 'Free', 'Free', '', 0.00, '/ month', '/ month', '/ month', '/ month', '', '', '', 0, 0, 1),
(2, 'Business', 'Business', 'Business', 'Business', '', 19.00, '/ month', '/ month', '/ month', '/ month', '', '', '', 1, 0, 2),
(3, 'Developer', 'Developer', 'Developer', 'Developer', '', 29.00, '/ month', '/ month', '/ month', '/ month', '', '', '', 0, 0, 3),
(4, 'Ultimate', 'Ultimate', 'Ultimate', 'Ultimate', '', 49.00, '/ month', '/ month', '/ month', '/ month', '', '', '', 0, 1, 4);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `plugins_raidplaner_attendance`
--

CREATE TABLE `plugins_raidplaner_attendance` (
  `id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `character_id` int(11) NOT NULL,
  `status` enum('Anwesend','Ersatzbank','Abwesend','Verspätet') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `plugins_raidplaner_bis_list`
--

CREATE TABLE `plugins_raidplaner_bis_list` (
  `id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `plugins_raidplaner_bosses`
--

CREATE TABLE `plugins_raidplaner_bosses` (
  `id` int(11) NOT NULL,
  `boss_name` varchar(255) NOT NULL,
  `sort_index` int(11) NOT NULL DEFAULT 0,
  `tactics` text DEFAULT NULL,
  `raid_id` int(11) NOT NULL DEFAULT 0,
  `template_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `plugins_raidplaner_bosses`
--

INSERT INTO `plugins_raidplaner_bosses` (`id`, `boss_name`, `sort_index`, `tactics`, `raid_id`, `template_id`) VALUES
(1, 'Demo Boss #1', 1, 'Demo Beschreibung', 0, NULL),
(2, 'Demo Boss #2', 2, 'Demo Beschreibung', 0, NULL);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `plugins_raidplaner_characters`
--

CREATE TABLE `plugins_raidplaner_characters` (
  `id` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `character_name` varchar(255) NOT NULL,
  `class_id` int(11) NOT NULL,
  `level` int(11) DEFAULT NULL,
  `is_main` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `plugins_raidplaner_character_gear`
--

CREATE TABLE `plugins_raidplaner_character_gear` (
  `id` int(11) NOT NULL,
  `character_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `is_obtained` tinyint(1) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `status_changed_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `plugins_raidplaner_classes`
--

CREATE TABLE `plugins_raidplaner_classes` (
  `id` int(11) NOT NULL,
  `class_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `plugins_raidplaner_classes`
--

INSERT INTO `plugins_raidplaner_classes` (`id`, `class_name`) VALUES
(1, 'Krieger'),
(2, 'Magier'),
(3, 'Jäger'),
(4, 'Schurke'),
(5, 'Priester'),
(6, 'Hexenmeister'),
(7, 'Paladin'),
(8, 'Druide'),
(9, 'Schamane'),
(10, 'Mönch'),
(11, 'Dämonenjäger'),
(12, 'Rufer'),
(13, 'Todesritter');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `plugins_raidplaner_events`
--

CREATE TABLE `plugins_raidplaner_events` (
  `id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `event_time` datetime NOT NULL,
  `duration_minutes` int(11) DEFAULT 180,
  `template_id` int(11) DEFAULT NULL,
  `created_by_user_id` int(11) NOT NULL DEFAULT 1,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `discord_message_id` varchar(30) DEFAULT NULL,
  `duration` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `plugins_raidplaner_events`
--

INSERT INTO `plugins_raidplaner_events` (`id`, `title`, `description`, `event_time`, `duration_minutes`, `template_id`, `created_by_user_id`, `is_active`, `discord_message_id`, `duration`) VALUES
(1, NULL, 'Demo Beschreibung', '2026-03-18 20:25:08', 180, 1, 1, 1, NULL, NULL);

--
-- Trigger `plugins_raidplaner_events`
--
DELIMITER $$
CREATE TRIGGER `raidevents_bi` BEFORE INSERT ON `plugins_raidplaner_events` FOR EACH ROW BEGIN
  IF NEW.template_id IS NOT NULL THEN
    SET NEW.title = NULL;
  ELSE
    IF NEW.title IS NULL OR NEW.title = '' THEN
      SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Titel ist erforderlich, wenn keine Vorlage gewaehlt ist';
    END IF;
  END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `raidevents_bu` BEFORE UPDATE ON `plugins_raidplaner_events` FOR EACH ROW BEGIN
  IF NEW.template_id IS NOT NULL THEN
    SET NEW.title = NULL;
  ELSE
    IF NEW.title IS NULL OR NEW.title = '' THEN
      SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Titel ist erforderlich, wenn keine Vorlage gewaehlt ist';
    END IF;
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `plugins_raidplaner_event_bosses`
--

CREATE TABLE `plugins_raidplaner_event_bosses` (
  `event_id` int(11) NOT NULL,
  `boss_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `plugins_raidplaner_event_bosses`
--

INSERT INTO `plugins_raidplaner_event_bosses` (`event_id`, `boss_id`) VALUES
(1, 1),
(1, 2);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `plugins_raidplaner_event_setup`
--

CREATE TABLE `plugins_raidplaner_event_setup` (
  `id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `count` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `plugins_raidplaner_event_setup`
--

INSERT INTO `plugins_raidplaner_event_setup` (`id`, `event_id`, `role_id`, `count`) VALUES
(1, 1, 1, 4),
(2, 1, 2, 10),
(3, 1, 3, 2);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `plugins_raidplaner_items`
--

CREATE TABLE `plugins_raidplaner_items` (
  `id` int(11) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `source` varchar(255) DEFAULT NULL,
  `boss_id` int(11) DEFAULT NULL,
  `boss_name` varchar(255) DEFAULT NULL,
  `raid_name` varchar(255) DEFAULT NULL,
  `slot` varchar(100) DEFAULT NULL,
  `class_spec` varchar(100) DEFAULT NULL,
  `is_bis` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `plugins_raidplaner_items`
--

INSERT INTO `plugins_raidplaner_items` (`id`, `item_name`, `source`, `boss_id`, `boss_name`, `raid_name`, `slot`, `class_spec`, `is_bis`) VALUES
(1, 'Demo Item', 'Drop: Demo Boss #1', 1, 'Demo Boss #1', 'Demo Raid', 'Demo Slot', NULL, 0);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `plugins_raidplaner_loot_distributed`
--

CREATE TABLE `plugins_raidplaner_loot_distributed` (
  `id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `character_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `plugins_raidplaner_loot_history`
--

CREATE TABLE `plugins_raidplaner_loot_history` (
  `id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `boss_id` int(11) DEFAULT NULL,
  `item_id` int(11) NOT NULL,
  `character_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `original_wish_status` int(1) DEFAULT NULL,
  `assigned_by` int(11) DEFAULT NULL,
  `looted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `plugins_raidplaner_participants`
--

CREATE TABLE `plugins_raidplaner_participants` (
  `id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `character_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `signup_status` enum('Angemeldet','Ersatzbank','Abgemeldet') NOT NULL,
  `attendance_status` enum('Anwesend','Abwesend') DEFAULT NULL,
  `comment` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `plugins_raidplaner_roles`
--

CREATE TABLE `plugins_raidplaner_roles` (
  `id` int(11) NOT NULL,
  `role_name` varchar(100) NOT NULL,
  `icon` varchar(64) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `plugins_raidplaner_roles`
--

INSERT INTO `plugins_raidplaner_roles` (`id`, `role_name`, `icon`) VALUES
(1, 'Healer', 'bi-heart-fill'),
(2, 'DD', 'bi-crosshair'),
(3, 'Tank', 'bi-shield-fill');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `plugins_raidplaner_settings`
--

CREATE TABLE `plugins_raidplaner_settings` (
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `plugins_raidplaner_settings`
--

INSERT INTO `plugins_raidplaner_settings` (`setting_key`, `setting_value`) VALUES
('discord_color_hex', '#C691AF'),
('discord_footer_text', 'Raidplaner'),
('discord_mention_role_id', ''),
('discord_ping_on_post', '0'),
('discord_show_date', '1'),
('discord_show_description', '1'),
('discord_show_roles', '1'),
('discord_show_signup', '1'),
('discord_show_time', '1'),
('discord_thumbnail_uploaded_url', ''),
('discord_thumbnail_url', ''),
('discord_title_prefix', '🗡️ '),
('discord_webhook_url', ''),
('manage_default_roles', '1');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `plugins_raidplaner_setup`
--

CREATE TABLE `plugins_raidplaner_setup` (
  `id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `needed_count` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `plugins_raidplaner_setup`
--

INSERT INTO `plugins_raidplaner_setup` (`id`, `event_id`, `role_id`, `needed_count`) VALUES
(1, 1, 1, 4),
(2, 1, 2, 10),
(3, 1, 3, 2);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `plugins_raidplaner_signups`
--

CREATE TABLE `plugins_raidplaner_signups` (
  `id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `character_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `status` enum('Angemeldet','Ersatzbank','Abgemeldet') NOT NULL,
  `comment` varchar(255) DEFAULT NULL,
  `signup_time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `plugins_raidplaner_templates`
--

CREATE TABLE `plugins_raidplaner_templates` (
  `id` int(11) NOT NULL,
  `template_name` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `duration_minutes` int(11) NOT NULL DEFAULT 180
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `plugins_raidplaner_templates`
--

INSERT INTO `plugins_raidplaner_templates` (`id`, `template_name`, `title`, `description`, `duration_minutes`) VALUES
(1, 'Demo Raid', 'Demo Raid', 'Demo Beschreibung', 180);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `plugins_raidplaner_template_bosses`
--

CREATE TABLE `plugins_raidplaner_template_bosses` (
  `template_id` int(11) NOT NULL,
  `boss_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `plugins_raidplaner_template_bosses`
--

INSERT INTO `plugins_raidplaner_template_bosses` (`template_id`, `boss_id`) VALUES
(1, 1),
(1, 2);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `plugins_raidplaner_template_setup`
--

CREATE TABLE `plugins_raidplaner_template_setup` (
  `template_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `needed_count` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `plugins_raidplaner_template_setup`
--

INSERT INTO `plugins_raidplaner_template_setup` (`template_id`, `role_id`, `needed_count`) VALUES
(1, 1, 4),
(1, 2, 10),
(1, 3, 2);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `plugins_raidplaner_wishlists`
--

CREATE TABLE `plugins_raidplaner_wishlists` (
  `id` int(11) NOT NULL,
  `participant_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `assigned_by` int(11) DEFAULT NULL,
  `assigned_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `plugins_rules`
--

CREATE TABLE `plugins_rules` (
  `id` int(11) NOT NULL,
  `content_key` varchar(50) NOT NULL,
  `language` char(2) NOT NULL,
  `content` mediumtext NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
  `userID` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `sort_order` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `plugins_rules`
--

INSERT INTO `plugins_rules` (`id`, `content_key`, `language`, `content`, `updated_at`, `userID`, `is_active`, `sort_order`) VALUES
(1, 'rule_1_title', 'de', '1', '2026-03-15 19:39:56', 0, 1, 0),
(2, 'rule_1_text', 'de', 'afcaf', '2026-03-15 19:39:56', 0, 1, 0),
(3, 'rule_2_title', 'de', '2', '2026-03-15 19:40:02', 0, 1, 0),
(4, 'rule_2_text', 'de', 'adDd', '2026-03-15 19:40:02', 0, 1, 0),
(5, 'rule_3_title', 'de', '3', '2026-03-15 19:40:10', 0, 1, 0),
(6, 'rule_3_text', 'de', 'DDd', '2026-03-15 19:40:10', 0, 1, 0),
(7, 'rule_4_title', 'de', '4', '2026-03-15 19:40:18', 0, 1, 0),
(8, 'rule_4_text', 'de', 'adada', '2026-03-15 19:40:18', 0, 1, 0),
(9, 'rule_5_title', 'de', '5', '2026-03-15 19:40:23', 0, 1, 0),
(10, 'rule_5_text', 'de', 'afaf', '2026-03-15 19:40:23', 0, 1, 0),
(11, 'rule_6_title', 'de', '6', '2026-03-15 19:40:29', 0, 1, 0),
(12, 'rule_6_text', 'de', 'afaf', '2026-03-15 19:40:29', 0, 1, 0),
(13, 'rule_7_title', 'de', '7', '2026-03-15 20:39:22', 1, 1, 0),
(14, 'rule_7_text', 'de', 'svsvsv', '2026-03-15 20:39:22', 1, 1, 0),
(15, 'rule_8_title', 'de', '8', '2026-03-15 20:39:07', 1, 1, 0),
(16, 'rule_8_text', 'de', 'gegeg', '2026-03-15 20:39:07', 1, 1, 0);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `plugins_rules_settings`
--

CREATE TABLE `plugins_rules_settings` (
  `rulessetID` int(11) NOT NULL,
  `rules` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Daten für Tabelle `plugins_rules_settings`
--

INSERT INTO `plugins_rules_settings` (`rulessetID`, `rules`) VALUES
(1, 5);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `plugins_shoutbox_messages`
--

CREATE TABLE `plugins_shoutbox_messages` (
  `id` int(10) UNSIGNED NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `username` varchar(50) NOT NULL,
  `message` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `plugins_shoutbox_messages`
--

INSERT INTO `plugins_shoutbox_messages` (`id`, `created_at`, `username`, `message`) VALUES
(1, '2026-03-08 16:56:08', 'T-Seven', 'test'),
(2, '2026-03-08 17:12:38', 'Gustaf', 'test 2'),
(3, '2026-03-08 17:32:40', 'Lucas', 'das ist ein Testeintrag'),
(4, '2026-03-16 19:41:41', 'gast', 'sqsq');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `plugins_sponsors`
--

CREATE TABLE `plugins_sponsors` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `level` enum('Platin Sponsor','Gold Sponsor','Silber Sponsor','Bronze Sponsor','Partner','Unterstützer') DEFAULT 'Unterstützer',
  `description` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `userID` int(11) NOT NULL,
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Daten für Tabelle `plugins_sponsors`
--

INSERT INTO `plugins_sponsors` (`id`, `name`, `slug`, `logo`, `level`, `description`, `updated_at`, `userID`, `sort_order`, `is_active`) VALUES
(1, 'Firma A', 'https://www.nexpell.de', '1.png', 'Platin Sponsor', NULL, '2025-06-01 11:46:22', 1, 6, 1),
(2, 'Firma B', 'https://www.nexpell.de', '2.png', 'Gold Sponsor', NULL, '2025-06-01 11:46:22', 1, 1, 1),
(3, 'Firma C', 'https://www.nexpell.de', '3.png', 'Silber Sponsor', NULL, '2025-06-01 11:46:22', 1, 2, 1),
(4, 'Firma D', 'https://www.nexpell.de', '4.png', 'Bronze Sponsor', NULL, '2025-06-01 11:46:22', 1, 3, 1),
(5, 'Firma E', 'https://www.nexpell.de', '5.png', 'Partner', NULL, '2025-06-01 11:46:22', 1, 4, 1),
(6, 'Firma F', 'https://www.nexpell.de', '6.png', 'Unterstützer', NULL, '2025-06-01 11:46:22', 1, 5, 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `plugins_sponsors_settings`
--

CREATE TABLE `plugins_sponsors_settings` (
  `sponsorssetID` int(11) NOT NULL,
  `sponsors` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Daten für Tabelle `plugins_sponsors_settings`
--

INSERT INTO `plugins_sponsors_settings` (`sponsorssetID`, `sponsors`) VALUES
(1, 5);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `plugins_teamspeak`
--

CREATE TABLE `plugins_teamspeak` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(100) NOT NULL,
  `host` varchar(255) NOT NULL,
  `query_port` smallint(5) UNSIGNED NOT NULL DEFAULT 10011,
  `server_port` smallint(5) UNSIGNED NOT NULL DEFAULT 9987,
  `query_user` varchar(100) NOT NULL,
  `query_pass` varchar(255) NOT NULL,
  `cache_time` int(10) UNSIGNED NOT NULL DEFAULT 60,
  `show_icons` tinyint(1) NOT NULL DEFAULT 1,
  `enabled` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `server_country` char(2) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `plugins_teamspeak`
--

INSERT INTO `plugins_teamspeak` (`id`, `title`, `host`, `query_port`, `server_port`, `query_user`, `query_pass`, `cache_time`, `show_icons`, `enabled`, `sort_order`, `server_country`, `updated_at`) VALUES
(2, 'Erster Test', '185.153.231.211', 10011, 9987, '', '', 60, 1, 1, 0, 'DE', '2026-03-06 23:05:46');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `plugins_todo`
--

CREATE TABLE `plugins_todo` (
  `id` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `assigned_to` int(11) DEFAULT NULL,
  `task` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `priority` enum('low','medium','high') NOT NULL DEFAULT 'medium',
  `due_date` datetime DEFAULT NULL,
  `done` tinyint(1) NOT NULL DEFAULT 0,
  `progress` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `plugins_todo`
--

INSERT INTO `plugins_todo` (`id`, `userID`, `assigned_to`, `task`, `description`, `priority`, `due_date`, `done`, `progress`, `created_at`, `updated_at`, `updated_by`) VALUES
(1, 1, 1, 'Download Plugin', 'rhrhrhrh', 'low', '2026-03-20 00:00:00', 0, 83, '2026-03-07 20:26:16', '2026-03-07 19:37:45', 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `plugins_twitch_banner_cache`
--

CREATE TABLE `plugins_twitch_banner_cache` (
  `channel` varchar(100) NOT NULL,
  `banner_url` text NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `plugins_twitch_settings`
--

CREATE TABLE `plugins_twitch_settings` (
  `id` int(11) NOT NULL,
  `main_channel` varchar(100) NOT NULL,
  `extra_channels` text NOT NULL,
  `client_id` varchar(255) NOT NULL DEFAULT '',
  `client_secret` varchar(255) NOT NULL DEFAULT '',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `plugins_twitch_settings`
--

INSERT INTO `plugins_twitch_settings` (`id`, `main_channel`, `extra_channels`, `client_id`, `client_secret`, `updated_at`) VALUES
(1, 'fl0m', 'zonixxcs,trilluxe,lowfuelmotorsport_en,glorious_e', 'aqwhys73slg9bjjnr7srw46732r6ik', '2snr4xt6nmvwnlem8y63lnera4n0di', '2026-03-06 21:11:58');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `plugins_userlist_settings`
--

CREATE TABLE `plugins_userlist_settings` (
  `id` int(11) NOT NULL,
  `users_per_page` int(11) DEFAULT 10,
  `users_widget_count` int(11) DEFAULT 5,
  `widget_show_online` tinyint(1) DEFAULT 1,
  `widget_sort` enum('lastlogin','registerdate','username') DEFAULT 'lastlogin',
  `show_avatars` tinyint(1) DEFAULT 1,
  `show_roles` tinyint(1) DEFAULT 1,
  `show_website` tinyint(1) DEFAULT 1,
  `show_lastlogin` tinyint(1) DEFAULT 1,
  `show_online_status` tinyint(1) DEFAULT 1,
  `show_registerdate` tinyint(1) DEFAULT 1,
  `default_sort` enum('username','registerdate','lastlogin','is_online','website') DEFAULT 'username',
  `default_order` enum('ASC','DESC') DEFAULT 'ASC',
  `enable_search` tinyint(1) DEFAULT 1,
  `enable_role_filter` tinyint(1) DEFAULT 1,
  `default_role` varchar(100) DEFAULT '',
  `pagination_style` enum('simple','full') DEFAULT 'full',
  `table_style` enum('striped','bordered','compact') DEFAULT 'striped',
  `avatar_size` enum('small','medium','large') DEFAULT 'small',
  `highlight_online_users` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Daten für Tabelle `plugins_userlist_settings`
--

INSERT INTO `plugins_userlist_settings` (`id`, `users_per_page`, `users_widget_count`, `widget_show_online`, `widget_sort`, `show_avatars`, `show_roles`, `show_website`, `show_lastlogin`, `show_online_status`, `show_registerdate`, `default_sort`, `default_order`, `enable_search`, `enable_role_filter`, `default_role`, `pagination_style`, `table_style`, `avatar_size`, `highlight_online_users`) VALUES
(1, 10, 5, 1, 'lastlogin', 1, 1, 1, 1, 1, 1, 'lastlogin', 'ASC', 1, 1, '', 'full', 'striped', 'large', 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `plugins_youtube`
--

CREATE TABLE `plugins_youtube` (
  `id` int(11) UNSIGNED NOT NULL,
  `plugin_name` varchar(50) NOT NULL,
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text NOT NULL,
  `is_first` tinyint(1) NOT NULL DEFAULT 0,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `plugins_youtube`
--

INSERT INTO `plugins_youtube` (`id`, `plugin_name`, `setting_key`, `setting_value`, `is_first`, `updated_at`) VALUES
(2, 'youtube', 'video_1', 'FAfxTvlq87s', 0, '2025-08-23 16:17:11'),
(6, 'youtube', 'video_2', 'PPQeNNvOdis', 0, '2025-08-23 18:25:48'),
(8, 'youtube', 'video_4', 'N6DW31S_oyI', 0, '2025-08-23 17:23:35'),
(9, 'youtube', 'video_5', 'hqQY9UkGC_A', 0, '2025-08-23 15:57:28'),
(10, 'youtube', 'video_6', 'ft4jcPSLJfY', 0, '2025-08-23 18:22:53'),
(11, 'youtube', 'video_7', '8wRW57nBLMI', 0, '2025-08-23 16:55:32'),
(12, 'youtube', 'video_8', 'a0nPjZkxCzQ', 0, '2025-08-23 16:16:04'),
(13, 'youtube', 'video_9', 'C3sW15lSAlM', 0, '2025-08-23 17:39:41'),
(14, 'youtube', 'video_10', 'wTUtBMMLseQ', 0, '2025-08-23 17:40:08'),
(15, 'youtube', 'video_11', 'ahzO3kqxP8Q', 1, '2025-08-23 18:48:50');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `plugins_youtube_settings`
--

CREATE TABLE `plugins_youtube_settings` (
  `id` int(11) NOT NULL,
  `plugin_name` varchar(50) NOT NULL,
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `plugins_youtube_settings`
--

INSERT INTO `plugins_youtube_settings` (`id`, `plugin_name`, `setting_key`, `setting_value`, `updated_at`) VALUES
(1, 'youtube', 'default_video_id', 'N6DW31S_oyI', '2025-08-23 16:49:00'),
(2, 'youtube', 'videos_per_page', '4', '2025-08-23 16:49:00'),
(3, 'youtube', 'videos_per_page_other', '6', '2025-08-23 16:49:00'),
(4, 'youtube', 'display_mode', 'grid', '2025-08-23 16:49:00'),
(5, 'youtube', 'first_full_width', '1', '2025-08-23 16:49:00');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `ratings`
--

CREATE TABLE `ratings` (
  `ratingID` int(11) NOT NULL,
  `plugin` varchar(50) NOT NULL,
  `itemID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `rating` tinyint(4) NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `settings`
--

CREATE TABLE `settings` (
  `settingID` int(11) NOT NULL,
  `hptitle` varchar(255) NOT NULL,
  `hpurl` varchar(255) NOT NULL,
  `clanname` varchar(255) NOT NULL,
  `clantag` varchar(255) NOT NULL,
  `adminname` varchar(255) NOT NULL,
  `adminemail` varchar(255) NOT NULL CHECK (`adminemail` like '%@%'),
  `since` year(4) NOT NULL DEFAULT 2025,
  `webkey` varchar(255) NOT NULL DEFAULT 'PLACEHOLDER_WEBKEY',
  `seckey` varchar(255) NOT NULL DEFAULT 'PLACEHOLDER_SECKEY',
  `closed` tinyint(1) NOT NULL DEFAULT 0,
  `default_language` varchar(5) NOT NULL DEFAULT 'de',
  `keywords` text NOT NULL,
  `startpage` varchar(255) NOT NULL,
  `use_seo_urls` tinyint(1) DEFAULT 0,
  `update_channel` enum('stable','beta','dev') NOT NULL DEFAULT 'stable',
  `forum_acl_debug` tinyint(1) NOT NULL DEFAULT 0,
  `twofa_force_all` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `settings`
--

INSERT INTO `settings` (`settingID`, `hptitle`, `hpurl`, `clanname`, `clantag`, `adminname`, `adminemail`, `since`, `webkey`, `seckey`, `closed`, `default_language`, `keywords`, `startpage`, `use_seo_urls`, `update_channel`, `forum_acl_debug`, `twofa_force_all`) VALUES
(1, 'nexpell', 'https://test.nexpell.de', 'Mein Clan / Verein', '[RM]', 'T-Seven', 'info@nexpell.de', '2025', '', '', 0, 'de', 'nexpell, CMS, Community-Management, Esport CMS, Webdesign, Clan-Design, Templates, Plugins, Addons, Mods, Anpassungen, Modifikationen, Tutorials, Downloads, Plugin-Entwicklung, Design-Anpassungen, Website-Builder, Digitales Projektmanagement', 'startpage', 0, 'beta', 0, 0);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `settings_content_lang`
--

CREATE TABLE `settings_content_lang` (
  `id` int(11) NOT NULL,
  `content_key` varchar(191) NOT NULL,
  `language` varchar(8) NOT NULL DEFAULT 'de',
  `content` mediumtext NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `settings_content_lang`
--

INSERT INTO `settings_content_lang` (`id`, `content_key`, `language`, `content`, `updated_at`) VALUES
(1, 'startpage_title', 'de', 'Next-Generation', '2026-03-02 16:54:08'),
(2, 'startpage', 'de', 'Willkommen bei nexpell!<br><br>Herzlichen Glückwunsch — die Installation von nexpell wurde erfolgreich abgeschlossen. Sie haben damit die Basis für eine moderne, flexible und leistungsstarke Webplattform geschaffen, die Ihnen alle Freiheiten bietet, Ihre Ideen zu verwirklichen. Ganz gleich, ob Sie einen Blog, eine Galerie, ein Forum oder eine umfassende Community-Plattform aufbauen möchten — mit nexpell haben Sie das passende Werkzeug in der Hand.<br><br><strong>👉 Ihre nächsten Schritte:</strong><br>- Melden Sie sich im Admin-Panel an, um Ihre ersten Seiten, Kategorien und Inhalte zu erstellen.<br>- Konfigurieren Sie Designs, Farben und Sprachoptionen ganz nach Ihrem Geschmack.<br>- Aktivieren Sie weitere Module wie Artikel, Bewertungen oder ein Diskussionsforum, um Ihre Besucher noch besser einzubinden.<br>- Nutzen Sie die eingebauten Statistik- und Analysefunktionen, um Ihre Zielgruppe besser zu verstehen und Ihre Website weiterzuentwickeln.<br><br>nexpell wurde entwickelt, damit Sie schnell und unkompliziert starten können — und gleichzeitig alle Möglichkeiten offen bleiben, Ihre Webpräsenz individuell zu gestalten.<br><br>Wir wünschen Ihnen viel Erfolg und vor allem Freude beim Aufbau Ihrer neuen Website!', '2026-03-02 16:54:08'),
(3, 'startpage', 'en', 'Welcome to nexpell!<br><br>Congratulations — the installation of nexpell has been successfully completed. You now have the foundation for a modern, flexible, and powerful web platform that gives you complete freedom to realize your ideas. Whether you want to build a blog, a gallery, a forum, or a comprehensive community platform — with nexpell, you have the right tool in hand.<br><br><strong>👉 Your next steps:</strong><br>- Log in to the admin panel to create your first pages, categories, and content.<br>- Configure designs, colors, and language options to your liking.<br>- Activate additional modules such as articles, reviews, or a discussion forum to better engage your visitors.<br>- Use the built-in statistics and analysis features to better understand your audience and further develop your website.<br><br>Nexpell was designed so you can start quickly and easily — while keeping all possibilities open to customize your web presence.<br><br>We wish you much success and, above all, joy in building your new website!', '2026-03-02 16:54:08'),
(4, 'startpage', 'it', 'Benvenuto in nexpell!<br><br>Congratulazioni — l\'installazione di nexpell è stata completata con successo. Ora hai le basi per una piattaforma web moderna, flessibile e potente che ti offre piena libertà di realizzare le tue idee. Che tu voglia creare un blog, una galleria, un forum o una piattaforma comunitaria completa — con nexpell hai lo strumento giusto a portata di mano.<br><br><strong>👉 I tuoi prossimi passi:</strong><br>- Accedi al pannello di amministrazione per creare le tue prime pagine, categorie e contenuti.<br>- Configura design, colori e opzioni linguistiche secondo i tuoi gusti.<br>- Attiva moduli aggiuntivi come articoli, recensioni o un forum di discussione per coinvolgere meglio i tuoi visitatori.<br>- Utilizza le funzioni statistiche e di analisi integrate per comprendere meglio il tuo pubblico e sviluppare ulteriormente il tuo sito.<br><br>Nexpell è stato progettato per permetterti di iniziare rapidamente e facilmente — mantenendo aperte tutte le possibilità per personalizzare la tua presenza sul web.<br><br>Ti auguriamo tanto successo e, soprattutto, gioia nella costruzione del tuo nuovo sito web!', '2026-03-02 16:54:08'),
(5, 'terms_of_service', 'de', '<img src=\"/images/uploads/nx_editor/nx_20260307_154403_cfddc115361d.jpg\" alt=\"\" style=\"width: 200px;\">ss', '2026-03-07 16:31:06'),
(10, 'privacy_policy', 'de', 'ss', '2026-03-07 15:29:39'),
(11, 'imprint', 'de', 'ss', '2026-03-07 15:30:00'),
(14, 'terms_of_service', 'it', '<img src=\"/images/uploads/nx_editor/nx_20260307_154416_933f8ca2c6e2.png\" alt=\"\" style=\"width: 300px;\">', '2026-03-07 16:31:29'),
(15, 'terms_of_service', 'en', 'hhh', '2026-03-07 15:37:59');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `settings_headstyle_config`
--

CREATE TABLE `settings_headstyle_config` (
  `id` int(10) UNSIGNED NOT NULL,
  `selected_style` varchar(64) NOT NULL DEFAULT 'head-style-1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `settings_headstyle_config`
--

INSERT INTO `settings_headstyle_config` (`id`, `selected_style`) VALUES
(1, 'head-boxes-4');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `settings_imprint`
--

CREATE TABLE `settings_imprint` (
  `id` int(11) NOT NULL,
  `type` varchar(255) NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `represented_by` varchar(255) NOT NULL,
  `tax_id` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `website` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `disclaimer` text DEFAULT NULL,
  `address` varchar(255) DEFAULT '',
  `postal_code` varchar(20) DEFAULT '',
  `city` varchar(100) DEFAULT '',
  `register_office` varchar(100) DEFAULT '',
  `register_number` varchar(100) DEFAULT '',
  `vat_id` varchar(50) DEFAULT '',
  `supervisory_authority` varchar(255) DEFAULT '',
  `editor` int(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `settings_imprint`
--

INSERT INTO `settings_imprint` (`id`, `type`, `company_name`, `represented_by`, `tax_id`, `email`, `website`, `phone`, `disclaimer`, `address`, `postal_code`, `city`, `register_office`, `register_number`, `vat_id`, `supervisory_authority`, `editor`) VALUES
(1, 'private', 'T-Seven', '', '', 'info@nexpell.de', '', '', '', 'ss', 'ss', 'sss', '', '', '', '', 0);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `settings_languages`
--

CREATE TABLE `settings_languages` (
  `id` int(10) UNSIGNED NOT NULL,
  `iso_639_1` char(2) NOT NULL COMMENT 'ISO 639-1 language code, z.B. "en"',
  `iso_639_2` char(3) DEFAULT NULL COMMENT 'Optional ISO 639-2 code, z.B. "eng"',
  `name_en` varchar(100) NOT NULL COMMENT 'Language name in English, z.B. "English"',
  `name_native` varchar(100) DEFAULT NULL COMMENT 'Native language name, z.B. "Deutsch"',
  `name_de` varchar(100) DEFAULT NULL COMMENT 'Language name in German, z.B. "Deutsch"',
  `active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Is the language active for selection',
  `flag` varchar(255) DEFAULT NULL COMMENT 'Pfad oder CSS-Klasse für Flagge, z.B. "/admin/images/flags/de.svg" oder "fi fi-de"',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `settings_languages`
--

INSERT INTO `settings_languages` (`id`, `iso_639_1`, `iso_639_2`, `name_en`, `name_native`, `name_de`, `active`, `flag`, `created_at`, `updated_at`) VALUES
(1, 'en', 'eng', 'English', 'English', 'Englisch', 1, '/admin/images/flags/gb.svg', '2026-03-02 15:46:37', NULL),
(2, 'de', 'deu', 'German', 'Deutsch', 'Deutsch', 1, '/admin/images/flags/de.svg', '2026-03-02 15:46:37', NULL),
(3, 'it', 'ita', 'Italian', 'Italiano', 'Italienisch', 1, '/admin/images/flags/it.svg', '2026-03-02 15:46:37', NULL),
(4, 'fr', 'fra', 'French', 'Français', 'Französisch', 0, '/admin/images/flags/fr.svg', '2026-03-02 15:46:37', NULL),
(5, 'es', 'spa', 'Spanish', 'Español', 'Spanisch', 0, '/admin/images/flags/es.svg', '2026-03-02 15:46:37', NULL),
(6, 'pt', 'por', 'Portuguese', 'Português', 'Portugiesisch', 0, '/admin/images/flags/pt.svg', '2026-03-02 15:46:37', NULL),
(7, 'pl', 'pol', 'Polish', 'Polski', 'Polnisch', 0, '/admin/images/flags/pl.svg', '2026-03-02 15:46:37', NULL),
(8, 'tr', 'tur', 'Turkish', 'Türkçe', 'Türkisch', 0, '/admin/images/flags/tr.svg', '2026-03-02 15:46:37', NULL);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `settings_plugins`
--

CREATE TABLE `settings_plugins` (
  `pluginID` int(10) UNSIGNED NOT NULL,
  `modulname` varchar(100) NOT NULL,
  `admin_file` varchar(255) DEFAULT NULL,
  `activate` tinyint(1) NOT NULL DEFAULT 1,
  `author` varchar(200) DEFAULT NULL,
  `website` varchar(200) DEFAULT NULL,
  `index_link` varchar(255) DEFAULT NULL,
  `hiddenfiles` text DEFAULT NULL,
  `version` varchar(20) DEFAULT '1.0',
  `path` varchar(255) NOT NULL,
  `status_display` tinyint(1) NOT NULL DEFAULT 1,
  `plugin_display` tinyint(1) NOT NULL DEFAULT 1,
  `widget_display` tinyint(1) NOT NULL DEFAULT 0,
  `delete_display` tinyint(1) NOT NULL DEFAULT 1,
  `sidebar` enum('deactivated','activated','full_activated') NOT NULL DEFAULT 'deactivated'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `settings_plugins`
--

INSERT INTO `settings_plugins` (`pluginID`, `modulname`, `admin_file`, `activate`, `author`, `website`, `index_link`, `hiddenfiles`, `version`, `path`, `status_display`, `plugin_display`, `widget_display`, `delete_display`, `sidebar`) VALUES
(1, 'startpage', '', 1, '', '', '', '', '', '', 0, 0, 1, 0, 'full_activated'),
(2, 'privacy_policy', '', 1, '', '', 'privacy_policy', '', '', '', 0, 0, 1, 0, 'deactivated'),
(3, 'imprint', '', 1, '', '', 'imprint', '', '', '', 0, 0, 1, 0, 'deactivated'),
(4, 'static', '', 1, '', '', 'static', '', '', '', 0, 0, 1, 0, 'deactivated'),
(5, 'error_404', '', 1, '', '', 'error_404', '', '', '', 0, 0, 1, 0, 'deactivated'),
(6, 'profile', '', 1, '', '', 'profile', '', '', '', 0, 0, 1, 0, 'deactivated'),
(7, 'login', '', 1, '', '', 'login', '', '', '', 0, 0, 1, 0, 'deactivated'),
(8, 'lostpassword', '', 1, '', '', 'lostpassword', '', '', '', 0, 0, 1, 0, 'deactivated'),
(9, 'contact', '', 1, '', '', 'contact', '', '', '', 0, 0, 1, 0, 'deactivated'),
(10, 'register', '', 1, '', '', 'register', '', '', '', 0, 0, 1, 0, 'deactivated'),
(11, 'edit_profile', '', 1, '', '', 'edit_profile,edit_profile_save', '', '', '', 0, 0, 1, 0, 'deactivated'),
(12, 'navigation', 'admin_navigation_settings', 1, 'T-Seven', 'https://www.nexpell.de', '', '', '0.3', 'includes/plugins/navigation/', 0, 0, 1, 0, 'deactivated'),
(13, 'footer', 'admin_footer', 1, 'T-Seven', 'https://www.nexpell.de', '', '', '0.1', 'includes/plugins/footer/', 0, 0, 1, 0, 'deactivated'),
(18, 'rules', 'admin_rules', 1, 'T-Seven', 'https://www.nexpell.de', 'rules', '', '0.1', 'includes/plugins/rules/', 1, 1, 1, 1, 'activated'),
(20, 'achievements', 'admin_achievements', 1, 'Fjolnd', 'https://www.nexpell.de', 'achievements', NULL, '1.0', 'includes/plugins/achievements/', 1, 1, 1, 1, 'activated'),
(21, 'about', 'admin_about', 1, 'T-Seven', 'https://www.nexpell.de', 'about,leistung,info', '', '1.0.1', 'includes/plugins/about/', 1, 1, 1, 1, 'deactivated'),
(22, 'carousel', 'admin_carousel', 1, 'T-Seven', 'https://www.nexpell.de', '', '', '0.1', 'includes/plugins/carousel/', 1, 1, 0, 1, 'deactivated'),
(25, 'userlist', 'admin_userlist', 1, 'T-Seven', 'https://www.nexpell.de', 'userlist', '', '0.1', 'includes/plugins/userlist/', 1, 1, 1, 1, 'deactivated'),
(27, 'partners', 'admin_partners', 1, 'T-Seven', 'https://www.nexpell.de', 'partners', '', '0.1', 'includes/plugins/partners/', 1, 1, 1, 1, 'deactivated'),
(29, 'discord', 'admin_discord', 1, 'T-Seven', 'https://www.nexpell.de', 'discord', '', '0.1', 'includes/plugins/discord/', 1, 1, 1, 1, 'deactivated'),
(35, 'articles', 'admin_articles', 1, 'T-Seven', 'https://www.nexpell.de', 'articles', '', '0.3', 'includes/plugins/articles/', 1, 1, 1, 1, 'deactivated'),
(42, 'todo', 'admin_todo', 1, 'T-Seven', 'https://www.nexpell.de', 'todo', '', '0.3', 'includes/plugins/todo/', 1, 1, 1, 1, 'deactivated'),
(46, 'twitch', 'admin_twitch', 1, 'T-Seven', 'https://www.nexpell.de', 'twitch', '', '0.1', 'includes/plugins/twitch/', 1, 1, 1, 1, 'deactivated'),
(47, 'youtube', 'admin_youtube', 1, 'T-Seven', 'https://www.nexpell.de', 'youtube', '', '0.3', 'includes/plugins/youtube/', 1, 1, 1, 1, 'deactivated'),
(48, 'counter', '', 1, 'T-Seven', 'https://webspell-rm.de', 'counter', '', '0.1', 'includes/plugins/counter/', 1, 1, 1, 1, 'deactivated'),
(49, 'downloads', 'admin_downloads,admin_download_stats', 1, 'T-Seven', 'https://www.nexpell.de', 'downloads', '', '0.3', 'includes/plugins/downloads/', 1, 1, 1, 1, 'deactivated'),
(50, 'sponsors', 'admin_sponsors', 1, 'T-Seven', 'https://www.nexpell.de', 'sponsors', '', '0.2', 'includes/plugins/sponsors/', 1, 1, 1, 1, 'deactivated'),
(51, 'teamspeak', 'admin_teamspeak', 1, 'T-Seven', 'https://webspell-rm.de', 'teamspeak', '', '1.0.0', 'includes/plugins/teamspeak/', 1, 1, 1, 1, 'deactivated'),
(52, 'gametracker', 'admin_gametracker', 1, 'T-Seven', 'https://www.nexpell.de', 'gametracker', '', '0.1', 'includes/plugins/gametracker/', 1, 1, 1, 1, 'deactivated'),
(57, 'lastlogin', 'admin_lastlogin', 1, 'T-Seven', 'https://www.nexpell.de', '', '', '0.1', 'includes/plugins/lastlogin/', 1, 1, 1, 1, 'deactivated'),
(58, 'messenger', '', 1, 'T-Seven', 'https://webspell-rm.de', 'messenger', '', '0.3', 'includes/plugins/messenger/', 1, 1, 1, 1, 'deactivated'),
(59, 'news', 'admin_news', 1, 'T-Seven', 'https://www.nexpell.de', 'news', '', '1.0', 'includes/plugins/news/', 1, 1, 1, 1, 'deactivated'),
(60, 'shoutbox', 'admin_shoutbox', 1, 'T-Seven', 'https://www.nexpell.de', 'shoutbox', '', '1.0.0', 'includes/plugins/shoutbox/', 1, 1, 0, 1, 'deactivated'),
(61, 'live_visitor', '', 1, 'T-Seven', 'https://webspell-rm.de', 'live_visitor', '', '0.1', 'includes/plugins/live_visitor/', 1, 1, 1, 1, 'deactivated'),
(62, 'gallery', 'admin_gallery', 1, 'T-Seven', 'https://webspell-rm.de', 'gallery', '', '0.1', 'includes/plugins/gallery/', 1, 1, 0, 1, 'deactivated'),
(63, 'joinus', 'admin_joinus', 1, 'T-Seven', 'https://webspell-rm.de', 'joinus', '', '1.0.0', 'includes/plugins/joinus/', 1, 1, 1, 1, 'deactivated'),
(64, 'masterlist', '', 1, 'T-Seven', 'https://www.nexpell.de', 'masterlist', '', '0.1', 'includes/plugins/masterlist/', 1, 1, 1, 1, 'deactivated'),
(65, 'pricing', 'admin_pricing', 1, 'T-Seven', 'https://www.nexpell.de', 'pricing', '', '0.1', 'includes/plugins/pricing/', 1, 1, 0, 1, 'deactivated'),
(66, 'raidplaner', 'admin_raidplaner', 1, 'Fjolnd', 'https://www.nexpell.de', 'raidplaner', '', '1.0.0', 'includes/plugins/raidplaner/', 1, 1, 1, 1, 'deactivated'),
(67, 'forum', 'admin_forum,admin_forum_permissions,admin_forum_permissions_ajax', 1, 'T-Seven', 'https://www.nexpell.de', 'forum,forum_boards,forum_category,forum_thread,forum_actions,', '', '1.0.1', 'includes/plugins/forum/', 1, 1, 1, 1, 'deactivated'),
(68, 'search', '', 1, 'T-Seven', 'https://www.nexpell.de', 'search', '', '0.1', 'includes/plugins/search/', 1, 1, 1, 1, 'deactivated'),
(69, 'links', 'admin_links', 1, 'T-Seven', 'https://webspell-rm.de', 'links,admin_links,links_rating', '', '0.1', 'includes/plugins/links/', 1, 1, 1, 1, 'deactivated');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `settings_plugins_installed`
--

CREATE TABLE `settings_plugins_installed` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `modulname` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `version` varchar(50) DEFAULT NULL,
  `author` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `folder` varchar(255) DEFAULT NULL,
  `installed_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `settings_plugins_installed`
--

INSERT INTO `settings_plugins_installed` (`id`, `name`, `modulname`, `description`, `version`, `author`, `url`, `folder`, `installed_date`) VALUES
(4, 'Rules', 'rules', '[[lang:de]]Das Rules Plugin verwaltet die wichtigen Regeln für Mitglieder, Server und den Verein, um ein faires und sicheres Miteinander zu gewährleisten.[[lang:en]]The Rules Plugin manages the important rules for members, servers, and the club to ensure fair and safe cooperation.[[lang:it]]Il plugin Regole gestisce le regole importanti per membri, server e associazione per garantire una convivenza equa e sicura.', '1.0.1', 'nexpell Team', 'https://www.nexpell.de', 'rules', '2026-03-04 18:38:39'),
(6, 'Achievements', 'achievements', '[[lang:de]]Das Achievements-Plugin ermöglicht es dir, besondere Leistungen, Meilensteine oder Aktivitäten deiner Nutzer in Form von Errungenschaften darzustellen. Ob für Community-Interaktionen, Engagement-Belohnungen oder spielerische Motivation – Achievements schaffen Anreize und erhöhen die Beteiligung auf deiner Website.&lt;br data-end=&quot;&amp;quot;\\&amp;quot;\\\\&amp;quot;744\\\\&amp;quot;\\&amp;quot;&amp;quot;&quot; data-start=&quot;&amp;quot;\\&amp;quot;\\\\&amp;quot;741\\\\&amp;quot;\\&amp;quot;&amp;quot;&quot;&gt;\nErrungenschaften lassen sich flexibel anlegen, bearbeiten und verwalten und können individuell an das Design sowie die Struktur deiner Plattform angepasst werden. Ideal für Community-Seiten, Foren, Projekte oder Gamification-basierte Websites.[[lang:en]]With the Achievements plugin, you can showcase special accomplishments, milestones, or activities of your users in the form of achievements. Whether used for community interaction, engagement rewards, or gamification purposes, achievements help motivate users and increase participation on your website.&lt;br data-end=&quot;&amp;quot;\\&amp;quot;\\\\&amp;quot;1508\\\\&amp;quot;\\&amp;quot;&amp;quot;&quot; data-start=&quot;&amp;quot;\\&amp;quot;\\\\&amp;quot;1505\\\\&amp;quot;\\&amp;quot;&amp;quot;&quot;&gt;\nAchievements can be easily created, edited, and managed, and can be adapted to match the design and structure of your platform. Perfect for community websites, forums, projects, or gamification-driven platforms.[[lang:it]]&lt;p data-end=&quot;&amp;quot;\\&amp;quot;\\\\&amp;quot;2457\\\\&amp;quot;\\&amp;quot;&amp;quot;&quot; data-start=&quot;&amp;quot;\\&amp;quot;\\\\&amp;quot;1931\\\\&amp;quot;\\&amp;quot;&amp;quot;&quot;&gt;Con il plugin Achievements puoi mostrare risultati speciali, obiettivi o attività degli utenti sotto forma di conquiste. Che si tratti di interazioni nella community, ricompense per l’attività o elementi di gamification, le achievements aiutano a motivare gli utenti e ad aumentare il coinvolgimento sul sito.&lt;br data-end=&quot;&amp;quot;\\&amp;quot;\\\\&amp;quot;2261\\\\&amp;quot;\\&amp;quot;&amp;quot;&quot; data-start=&quot;&amp;quot;\\&amp;quot;\\\\&amp;quot;2258\\\\&amp;quot;\\&amp;quot;&amp;quot;&quot;&gt;\nLe conquiste possono essere create, modificate e gestite facilmente e adattate al design e alla struttura della piattaforma. Ideale per community, forum, progetti o siti basati sulla gamification.&lt;/p&gt;\n', '1.0.0', 'nexpell-team', 'https://www.nexpell.de', 'achievements', '2026-03-04 20:21:11'),
(7, 'Über uns', 'about', 'Mit diesem Plugin könnt ihr eure Über-uns-Seite anzeigen lassen.', '1.0.3', 'nexpell Team', 'https://www.nexpell.de', 'about', '2026-03-15 15:01:32'),
(8, 'Carousel', 'carousel', '[[lang:de]]Dieses Plugin erweitert Nexpell um moderne und flexible Darstellungsmöglichkeiten wie &lt;strong data-end=&quot;355&quot; data-start=&quot;343&quot;&gt;Carousel&lt;/strong&gt;, &lt;strong data-end=&quot;367&quot; data-start=&quot;357&quot;&gt;Agency&lt;/strong&gt;, &lt;strong data-end=&quot;381&quot; data-start=&quot;369&quot;&gt;Parallax&lt;/strong&gt; und &lt;strong data-end=&quot;396&quot; data-start=&quot;386&quot;&gt;Sticky&lt;/strong&gt;. Inhalte können als dynamische Slider präsentiert werden, um Highlights, Bilder oder Texte wirkungsvoll in Szene zu setzen.&lt;br data-end=&quot;523&quot; data-start=&quot;520&quot; /&gt;\nMit der Agency-Funktion lässt sich ein großflächiges Hero-Bild oberhalb der Navigation anzeigen, wobei die Navigation zunächst transparent ist und sich beim Scrollen automatisch anpasst. Parallax-Effekte sorgen zusätzlich für eine ansprechende Tiefenwirkung beim Scrollen, während die Sticky-Funktion wichtige Elemente dauerhaft sichtbar hält. Ideal für moderne Startseiten, Landingpages und visuell orientierte Websites.[[lang:en]]This plugin extends Nexpell with modern and flexible display features such as &lt;strong data-end=&quot;1058&quot; data-start=&quot;1046&quot;&gt;Carousel&lt;/strong&gt;, &lt;strong data-end=&quot;1070&quot; data-start=&quot;1060&quot;&gt;Agency&lt;/strong&gt;, &lt;strong data-end=&quot;1084&quot; data-start=&quot;1072&quot;&gt;Parallax&lt;/strong&gt;, and &lt;strong data-end=&quot;1100&quot; data-start=&quot;1090&quot;&gt;Sticky&lt;/strong&gt;. Content can be presented in dynamic sliders to showcase highlights, images, or text in an engaging way.&lt;br data-end=&quot;1208&quot; data-start=&quot;1205&quot; /&gt;\nThe Agency feature adds a large hero image above the navigation, with the navigation initially transparent and automatically adapting while scrolling. Parallax effects enhance the visual depth of the page, and the sticky feature keeps important elements visible at all times. Perfect for modern homepages, landing pages, and visually focused websites.[[lang:it]]Questo plugin estende Nexpell con funzionalità di visualizzazione moderne e flessibili come &lt;strong data-end=&quot;1688&quot; data-start=&quot;1676&quot;&gt;Carousel&lt;/strong&gt;, &lt;strong data-end=&quot;1700&quot; data-start=&quot;1690&quot;&gt;Agency&lt;/strong&gt;, &lt;strong data-end=&quot;1714&quot; data-start=&quot;1702&quot;&gt;Parallax&lt;/strong&gt; e &lt;strong data-end=&quot;1727&quot; data-start=&quot;1717&quot;&gt;Sticky&lt;/strong&gt;. I contenuti possono essere presentati tramite slider dinamici per valorizzare immagini, testi o contenuti in evidenza.&lt;br data-end=&quot;1850&quot; data-start=&quot;1847&quot; /&gt;\nLa funzione Agency consente di visualizzare un’immagine hero sopra la navigazione, che inizialmente è trasparente e si adatta automaticamente durante lo scorrimento. Gli effetti parallax aggiungono profondità visiva alla pagina, mentre la funzione sticky mantiene sempre visibili gli elementi più importanti. Ideale per homepage moderne, landing page e siti web con un forte impatto visivo.', '1.0.3', 'nexpell Team', 'https://www.nexpell.de', 'carousel', '2026-03-15 15:01:39'),
(11, 'User List', 'userlist', '[[lang:de]]User List Plugin für nexpell – enthält zusätzlich Counter- und WhoIsOnline-Funktionalität.[[lang:en]]User List plugin for nexpell – now also includes Counter and WhoIsOnline functionality.[[lang:it]]Plugin Lista Utenti per nexpell – include inoltre le funzionalità di Counter e WhoIsOnline.', '1.0.3', 'nexpell Team', 'https://www.nexpell.de', 'userlist', '2026-03-15 15:08:30'),
(12, 'Counter', 'counter', 'With this plugin you can display your counter and visitor statistics.', '1.0.3', 'T-Seven', 'https://www.nexpell.de', 'counter', '2026-03-15 15:01:42'),
(13, 'Partners', 'partners', '[[lang:de]]Unsere Partnerseite bietet die Möglichkeit, eure Partner mit nützlichen Inhalten und praktischen Tools zu verlinken.[[lang:en]]Our partners page offers the opportunity to link your partners with useful content and practical tools.[[lang:it]]La nostra pagina partner offre la possibilità di collegare i vostri partner con contenuti utili e strumenti pratici.', '1.0.3', 'nexpell Team', 'https://www.nexpell.de', 'partners', '2026-03-15 15:05:33'),
(15, 'Discord', 'discord', '[[lang:de]]Dieses Plugin zeigt die aktuell &lt;strong data-end=&quot;416&quot; data-start=&quot;382&quot;&gt;online befindlichen Mitglieder&lt;/strong&gt; deines Discord-Servers direkt auf deiner Nexpell-Website an. Besucher erhalten so einen schnellen Überblick darüber, wie aktiv die Community gerade ist.&lt;br data-end=&quot;572&quot; data-start=&quot;569&quot; /&gt;\nEin integrierter &lt;strong data-end=&quot;607&quot; data-start=&quot;589&quot;&gt;Connect-Button&lt;/strong&gt; ermöglicht es Nutzern, mit einem Klick deinem Discord-Server beizutreten. Das Plugin eignet sich ideal für Community-Websites, Support-Seiten oder Projekte, die ihre Discord-Community sichtbar und leicht erreichbar machen möchten.[[lang:en]]This plugin displays the &lt;strong data-end=&quot;933&quot; data-start=&quot;905&quot;&gt;currently online members&lt;/strong&gt; of your Discord server directly on your Nexpell website. Visitors can quickly see how active the community is at the moment.&lt;br data-end=&quot;1061&quot; data-start=&quot;1058&quot; /&gt;\nAn integrated &lt;strong data-end=&quot;1093&quot; data-start=&quot;1075&quot;&gt;connect button&lt;/strong&gt; allows users to join your Discord server with a single click. The plugin is ideal for community websites, support pages, or projects that want to make their Discord community visible and easily accessible.[[lang:it]]Questo plugin mostra i &lt;strong data-end=&quot;1394&quot; data-start=&quot;1365&quot;&gt;membri attualmente online&lt;/strong&gt; del tuo server Discord direttamente sul tuo sito Nexpell. I visitatori possono vedere rapidamente quanto è attiva la community in quel momento.&lt;br data-end=&quot;1541&quot; data-start=&quot;1538&quot; /&gt;\nUn &lt;strong data-end=&quot;1571&quot; data-start=&quot;1544&quot;&gt;pulsante di connessione&lt;/strong&gt; integrato consente agli utenti di unirsi al server Discord con un solo clic. Il plugin è ideale per siti community, pagine di supporto o progetti che desiderano rendere visibile e facilmente accessibile la propria community Discord.', '1.0.3', 'nexpell-team', 'https://www.nexpell.de', 'discord', '2026-03-15 15:01:45'),
(16, 'Download', 'downloads', 'With this plugin you can display your downloads.', '1.0.3', 'T-Seven', 'https://www.nexpell.de', 'downloads', '2026-03-15 15:01:48'),
(17, 'Twitch', 'twitch', '[[lang:de]]Zeigt deinen Twitch-Livestream direkt auf deiner Website mit integriertem Chat.[[lang:gb]]Display your Twitch livestream directly on your website with integrated chat.[[lang:it]]Mostra il tuo livestream Twitch direttamente sul tuo sito web con chat integrata.', '1.0.3', 'nexpell-team', 'https://www.nexpell.de', 'twitch', '2026-03-15 15:08:21'),
(18, 'TeamSpeak', 'teamspeak', '[[lang:de]]Das TeamSpeak-Plugin für Nexpell ermöglicht die Anzeige eines oder mehrerer TeamSpeak-Server inklusive Channel-Struktur und aktiver Nutzer. Es stellt eine übersichtliche Darstellung der Serverinformationen bereit und zeigt in Echtzeit, welche Channels existieren und welche Nutzer online sind. Das Plugin ist auf Stabilität und Datenschutz ausgelegt. Es verarbeitet ausschließlich serverseitige Informationen und verzichtet vollständig auf clientbezogene Tracking-Daten. Optional können zusätzliche Informationen wie Server-Standort oder Statusanzeigen ergänzt werden. Durch integriertes Caching wird die Serverlast reduziert und eine zuverlässige Anzeige gewährleistet.[[lang:en]]The TeamSpeak plugin for Nexpell allows the display of one or more TeamSpeak servers including channel structure and active users. It provides a clear overview of server information and shows in real time which channels exist and which users are online. The plugin is designed with stability and data protection in mind. It processes only server-side information and completely avoids client-based tracking data. Optional information such as server location or status indicators can be added. Integrated caching reduces server load and ensures reliable performance.[[lang:it]]Il plugin TeamSpeak per Nexpell consente di visualizzare uno o più server TeamSpeak, inclusa la struttura dei canali e gli utenti attivi. Offre una panoramica chiara delle informazioni del server e mostra in tempo reale quali canali esistono e quali utenti sono online. Il plugin è progettato ponendo particolare attenzione alla stabilità e alla protezione dei dati. Elabora esclusivamente informazioni lato server ed evita completamente il tracciamento degli utenti. È possibile aggiungere informazioni opzionali come la posizione del server o indicatori di stato. Il caching integrato riduce il carico del server e garantisce un funzionamento affidabile.', '1.0.0', 'nexpell-team', 'https://www.nexpell.de', 'teamspeak', '2026-03-08 11:41:49'),
(21, 'Articles', 'articles', '[[lang:de]]Das Artikel-Plugin bietet dir ein leistungsstarkes Werkzeug zur Verwaltung redaktioneller Inhalte auf deiner Website. Artikel lassen sich übersichtlich anlegen, bearbeiten und kategorisieren, sodass Inhalte klar strukturiert und für Besucher leicht auffindbar sind.&lt;br data-end=&quot;675&quot; data-start=&quot;672&quot; /&gt;\nNeben der klassischen Artikelverwaltung unterstützt das Plugin Kommentare und Bewertungen, um Interaktionen mit deinen Lesern zu fördern. Dadurch eignet sich das Artikel-Plugin ideal für News-Bereiche, Blogs, Magazine oder projektbezogene Informationsseiten. Die Inhalte können flexibel an das Design und die Struktur deiner Nexpell-Website angepasst werden.[[lang:en]]The Article plugin provides a powerful solution for managing editorial content on your website. Articles can be created, edited, and organized into categories, making it easy for visitors to navigate and find relevant information.&lt;br data-end=&quot;1459&quot; data-start=&quot;1456&quot; /&gt;\nIn addition to standard article management, the plugin supports comments and ratings to encourage reader interaction and engagement. This makes the Article plugin an excellent choice for news sections, blogs, online magazines, or project-focused information pages. Content can be flexibly adapted to the design and structure of your Nexpell website.[[lang:it]]Il plugin Articoli offre uno strumento potente per la gestione dei contenuti editoriali sul tuo sito web. Gli articoli possono essere creati, modificati e organizzati in categorie, permettendo ai visitatori di trovare facilmente le informazioni desiderate.&lt;br data-end=&quot;2283&quot; data-start=&quot;2280&quot; /&gt;\nOltre alla gestione classica degli articoli, il plugin supporta commenti e valutazioni per favorire l’interazione e il coinvolgimento dei lettori. È quindi ideale per sezioni news, blog, magazine online o pagine informative dedicate a progetti specifici. I contenuti possono essere adattati in modo flessibile al design e alla struttura del tuo sito Nexpell.', '1.0.3', 'nexpell Team', 'https://www.nexpell.de', 'articles', '2026-03-15 15:01:35'),
(22, 'GameTracker', 'gametracker', 'Displays information about your game servers such as name, map, player count, and status directly on your website.', '1.0.3', 'nexpell-team', 'https://www.nexpell.de', 'gametracker', '2026-03-15 15:02:28'),
(23, 'Youtube', 'youtube', 'With this plugin you can display your Youtube videos.', '1.0.3', 'nexpell-team', 'https://www.nexpell.de', 'youtube', '2026-03-15 15:08:37'),
(24, 'Todo', 'todo', '[[lang:de]]Füge deiner Website eine einfache Todo-Liste hinzu, um Aufgaben zu verwalten und zu organisieren.[[lang:en]]Add a simple to-do list to your website to manage and organize tasks.[[lang:it]]Aggiungi una semplice lista di cose da fare al tuo sito per gestire e organizzare le attività.', '1.0.3', 'nexpell-team', 'https://www.nexpell.de', 'todo', '2026-03-15 15:08:14'),
(25, 'Sponsors', 'sponsors', 'With this plugin you can display your sponsors.', '1.0.3', 'nexpell-team', 'https://www.nexpell.de', 'sponsors', '2026-03-15 15:08:06'),
(40, 'Lastlogin', 'lastlogin', '[[lang:de]]Die Funktion &lt;strong data-end=&quot;315&quot; data-start=&quot;293&quot;&gt;„Letzte Anmeldung“&lt;/strong&gt; zeigt eine detaillierte Übersicht über den Login- und Aktivitätsstatus aller registrierten Benutzer. Angezeigt werden unter anderem der &lt;strong data-end=&quot;478&quot; data-start=&quot;452&quot;&gt;letzte Login-Zeitpunkt&lt;/strong&gt;, die &lt;strong data-end=&quot;531&quot; data-start=&quot;484&quot;&gt;vergangenen Tage seit der letzten Anmeldung&lt;/strong&gt;, der &lt;strong data-end=&quot;552&quot; data-start=&quot;537&quot;&gt;Aktivstatus&lt;/strong&gt;, die &lt;strong data-end=&quot;576&quot; data-start=&quot;558&quot;&gt;E-Mail-Adresse&lt;/strong&gt; sowie das &lt;strong data-end=&quot;610&quot; data-start=&quot;587&quot;&gt;Registrierungsdatum&lt;/strong&gt;.&lt;br data-end=&quot;614&quot; data-start=&quot;611&quot; /&gt;\nDiese Informationen dienen ausschließlich der &lt;strong data-end=&quot;678&quot; data-start=&quot;660&quot;&gt;Administration&lt;/strong&gt; und sind &lt;strong data-end=&quot;719&quot; data-start=&quot;688&quot;&gt;nur im Admincenter sichtbar&lt;/strong&gt;. Sie unterstützen Administratoren dabei, Nutzeraktivität zu überwachen, inaktive Accounts zu erkennen und die Benutzerverwaltung effizient und sicher zu steuern.[[lang:en]]The &lt;strong data-end=&quot;941&quot; data-start=&quot;927&quot;&gt;Last Login&lt;/strong&gt; feature provides administrators with a detailed overview of user login and activity status. It displays information such as the &lt;strong data-end=&quot;1098&quot; data-start=&quot;1070&quot;&gt;last login date and time&lt;/strong&gt;, &lt;strong data-end=&quot;1125&quot; data-start=&quot;1100&quot;&gt;days since last login&lt;/strong&gt;, &lt;strong data-end=&quot;1146&quot; data-start=&quot;1127&quot;&gt;activity status&lt;/strong&gt;, &lt;strong data-end=&quot;1165&quot; data-start=&quot;1148&quot;&gt;email address&lt;/strong&gt;, and &lt;strong data-end=&quot;1192&quot; data-start=&quot;1171&quot;&gt;registration date&lt;/strong&gt;.&lt;br data-end=&quot;1196&quot; data-start=&quot;1193&quot; /&gt;\nThis data is intended &lt;strong data-end=&quot;1249&quot; data-start=&quot;1218&quot;&gt;for administrative use only&lt;/strong&gt; and is &lt;strong data-end=&quot;1304&quot; data-start=&quot;1257&quot;&gt;visible exclusively within the admin center&lt;/strong&gt;. It helps administrators monitor user activity, identify inactive accounts, and manage users efficiently and securely.[[lang:it]]La funzione &lt;strong data-end=&quot;1496&quot; data-start=&quot;1478&quot;&gt;Ultimo accesso&lt;/strong&gt; offre agli amministratori una panoramica dettagliata dello stato di accesso e attività degli utenti registrati. Vengono mostrati dati come la &lt;strong data-end=&quot;1675&quot; data-start=&quot;1639&quot;&gt;data e l’ora dell’ultimo accesso&lt;/strong&gt;, i &lt;strong data-end=&quot;1717&quot; data-start=&quot;1679&quot;&gt;giorni trascorsi dall’ultimo login&lt;/strong&gt;, lo &lt;strong data-end=&quot;1743&quot; data-start=&quot;1722&quot;&gt;stato di attività&lt;/strong&gt;, l’&lt;strong data-end=&quot;1766&quot; data-start=&quot;1747&quot;&gt;indirizzo email&lt;/strong&gt; e la &lt;strong data-end=&quot;1797&quot; data-start=&quot;1772&quot;&gt;data di registrazione&lt;/strong&gt;.&lt;br data-end=&quot;1801&quot; data-start=&quot;1798&quot; /&gt;\nQueste informazioni sono destinate &lt;strong data-end=&quot;1874&quot; data-start=&quot;1836&quot;&gt;esclusivamente all’amministrazione&lt;/strong&gt; e sono &lt;strong data-end=&quot;1931&quot; data-start=&quot;1882&quot;&gt;visibili solo nel pannello di amministrazione&lt;/strong&gt;. La funzione aiuta a monitorare l’attività degli utenti, individuare account inattivi e gestire la piattaforma in modo sicuro ed efficiente.', '1.0.3', 'nexpell Team', 'https://www.nexpell.de', 'lastlogin', '2026-03-15 15:02:33'),
(41, 'Messenger', 'messenger', 'With this plugin you can send and receive private messages between users.', '1.0.3', 'nexpell-team', 'https://www.nexpell.de', 'messenger', '2026-03-15 15:05:15'),
(42, 'News', 'news', '[[lang:de]]News Plugin für nexpell, Kommentarfunktion noch nicht aktiv und muss deaktiviert werden.[[lang:en]]News plugin for nexpell, comment function not yet active and must be deactivated.[[lang:it]]Plugin News per nexpell, la funzione di commento non è ancora attiva e deve essere disattivata.', '1.0.3', 'nexpell Team', 'https://www.nexpell.de', 'news', '2026-03-15 15:05:26'),
(43, 'Shoutbox', 'shoutbox', '[[lang:de]]Füge deiner Website eine einfache Shoutbox hinzu, um kurze Nachrichten mit deinen Besuchern auszutauschen.[[lang:gb]]Add a simple shoutbox to your website to exchange short messages with your visitors.[[lang:it]]Aggiungi una semplice shoutbox al tuo sito per scambiare brevi messaggi con i visitatori.', '1.0.3', 'nexpell-team', 'https://www.nexpell.de', 'shoutbox', '2026-03-15 15:06:14'),
(44, 'Live Visitor', 'live_visitor', 'With this plugin you can display your live visitors, Who is Online and visitor statistics.', '1.0.3', 'nexpell-team', 'https://www.nexpell.de', 'live_visitor', '2026-03-15 15:04:59'),
(45, 'Gallery', 'gallery', 'With this plugin you can display a gallery on the website.', '1.0.3', 'T-Seven', 'https://www.nexpell.de', 'gallery', '2026-03-15 15:02:22'),
(46, 'JoinUs', 'joinus', '[[lang:de]]Das JoinUs-Plugin ermöglicht es Besuchern und Community-Mitgliedern, sich strukturiert für Team-, Squad- oder Partnerrollen zu bewerben. Administratoren können flexibel festlegen, welche Rollen, Squads und Bewerbungstypen zur Auswahl stehen. Jede Bewerbung wird zentral im Adminbereich verwaltet und kann dort geprüft, kommentiert, freigegeben oder abgelehnt werden. Statusänderungen lassen sich optional automatisch per E-Mail an den Bewerber kommunizieren. Das Plugin unterstützt individuelle Rollenangaben, eine saubere Trennung zwischen Systemrollen und JoinUs-Rollen sowie reCAPTCHA-Absicherung für Gäste. JoinUs bietet damit eine sichere, übersichtliche und erweiterbare Lösung für Community-Aufbau, Team-Recruiting und Partnerschaften.[[lang:en]]The JoinUs plugin allows visitors and community members to apply in a structured way for team, squad, or partner roles. Administrators can flexibly define which roles, squads, and application types are available. All applications are centrally managed in the admin panel, where they can be reviewed, commented on, accepted, or rejected. Status changes can optionally be communicated to applicants automatically via email. The plugin supports custom role entries, a clear separation between system roles and JoinUs roles, and reCAPTCHA protection for guests. JoinUs therefore provides a secure, well-organized, and extensible solution for community growth, team recruiting, and partnerships.[[lang:it]]Il plugin JoinUs consente ai visitatori e ai membri della community di candidarsi in modo strutturato per ruoli di team, squad o partnership. Gli amministratori possono definire in modo flessibile quali ruoli, squad e tipi di candidatura sono disponibili. Tutte le candidature vengono gestite centralmente nel pannello di amministrazione, dove possono essere esaminate, commentate, accettate o rifiutate. Le modifiche di stato possono essere comunicate automaticamente al candidato tramite e-mail. Il plugin supporta ruoli personalizzati, una chiara separazione tra ruoli di sistema e ruoli JoinUs e la protezione reCAPTCHA per gli ospiti. JoinUs offre quindi una soluzione sicura, chiara ed estendibile per la crescita della community, il recruiting dei team e le collaborazioni.', '1.0.0', 'nexpell-team', 'https://www.nexpell.de', 'joinus', '2026-03-09 20:59:50'),
(47, 'Masterlist', 'masterlist', '[[lang:de]]Die Masterliste ermöglicht eine umfassende Übersicht über öffentliche Gameserver verschiedener &lt;strong data-end=&quot;430&quot; data-start=&quot;404&quot;&gt;Call of Duty-Versionen&lt;/strong&gt;. Server lassen sich nach Spiel und Version filtern, zum Beispiel &lt;strong data-end=&quot;505&quot; data-start=&quot;496&quot;&gt;COD 1&lt;/strong&gt;, &lt;strong data-end=&quot;517&quot; data-start=&quot;507&quot;&gt;COD UO&lt;/strong&gt;, &lt;strong data-end=&quot;528&quot; data-start=&quot;519&quot;&gt;COD 2&lt;/strong&gt; oder &lt;strong data-end=&quot;543&quot; data-start=&quot;534&quot;&gt;COD 4&lt;/strong&gt;, wobei die gewünschte Version bequem über eine Auswahlbox gewählt werden kann.&lt;br data-end=&quot;625&quot; data-start=&quot;622&quot; /&gt;\nIn der Listenansicht werden wichtige Informationen wie &lt;strong data-end=&quot;694&quot; data-start=&quot;680&quot;&gt;Servername&lt;/strong&gt;, &lt;strong data-end=&quot;711&quot; data-start=&quot;696&quot;&gt;IP und Port&lt;/strong&gt;, &lt;strong data-end=&quot;729&quot; data-start=&quot;713&quot;&gt;aktuelle Map&lt;/strong&gt;, &lt;strong data-end=&quot;748&quot; data-start=&quot;731&quot;&gt;Spieleranzahl&lt;/strong&gt; und &lt;strong data-end=&quot;763&quot; data-start=&quot;753&quot;&gt;Status&lt;/strong&gt; angezeigt. Jeder Eintrag kann zudem &lt;strong data-end=&quot;815&quot; data-start=&quot;800&quot;&gt;aufgeklappt&lt;/strong&gt; werden, um eine &lt;strong data-end=&quot;849&quot; data-start=&quot;832&quot;&gt;Detailansicht&lt;/strong&gt; mit weiteren Informationen anzuzeigen, darunter Map-Vorschau, Gametype sowie eine &lt;strong data-end=&quot;972&quot; data-start=&quot;932&quot;&gt;Live-Spielerliste mit Score und Ping&lt;/strong&gt;.&lt;br data-end=&quot;976&quot; data-start=&quot;973&quot; /&gt;\nDie Masterliste eignet sich ideal für Clans, Communities und Gaming-Websites, die aktuelle Serverdaten übersichtlich darstellen und Spielern einen schnellen Überblick über aktive Server bieten möchten.[[lang:en]]The Masterlist provides a comprehensive overview of public game servers across different &lt;strong data-end=&quot;1333&quot; data-start=&quot;1308&quot;&gt;Call of Duty versions&lt;/strong&gt;. Servers can be filtered by game and version, such as &lt;strong data-end=&quot;1397&quot; data-start=&quot;1388&quot;&gt;COD 1&lt;/strong&gt;, &lt;strong data-end=&quot;1409&quot; data-start=&quot;1399&quot;&gt;COD UO&lt;/strong&gt;, &lt;strong data-end=&quot;1420&quot; data-start=&quot;1411&quot;&gt;COD 2&lt;/strong&gt;, or &lt;strong data-end=&quot;1434&quot; data-start=&quot;1425&quot;&gt;COD 4&lt;/strong&gt;, using a convenient version selector.&lt;br data-end=&quot;1475&quot; data-start=&quot;1472&quot; /&gt;\nThe list view displays key information including &lt;strong data-end=&quot;1539&quot; data-start=&quot;1524&quot;&gt;server name&lt;/strong&gt;, &lt;strong data-end=&quot;1556&quot; data-start=&quot;1541&quot;&gt;IP and port&lt;/strong&gt;, &lt;strong data-end=&quot;1573&quot; data-start=&quot;1558&quot;&gt;current map&lt;/strong&gt;, &lt;strong data-end=&quot;1591&quot; data-start=&quot;1575&quot;&gt;player count&lt;/strong&gt;, and &lt;strong data-end=&quot;1614&quot; data-start=&quot;1597&quot;&gt;server status&lt;/strong&gt;. Each server entry can be &lt;strong data-end=&quot;1653&quot; data-start=&quot;1641&quot;&gt;expanded&lt;/strong&gt; to show a &lt;strong data-end=&quot;1681&quot; data-start=&quot;1664&quot;&gt;detailed view&lt;/strong&gt; with additional information such as a map preview, game type, and a &lt;strong data-end=&quot;1790&quot; data-start=&quot;1750&quot;&gt;live player list with score and ping&lt;/strong&gt;.&lt;br data-end=&quot;1794&quot; data-start=&quot;1791&quot; /&gt;\nThe Masterlist is ideal for clans, communities, and gaming websites that want to present up-to-date server information and give players a quick overview of active servers.[[lang:it]]La Masterlist offre una panoramica completa dei server di gioco pubblici per diverse &lt;strong data-end=&quot;2121&quot; data-start=&quot;2093&quot;&gt;versioni di Call of Duty&lt;/strong&gt;. I server possono essere filtrati per gioco e versione, come &lt;strong data-end=&quot;2192&quot; data-start=&quot;2183&quot;&gt;COD 1&lt;/strong&gt;, &lt;strong data-end=&quot;2204&quot; data-start=&quot;2194&quot;&gt;COD UO&lt;/strong&gt;, &lt;strong data-end=&quot;2215&quot; data-start=&quot;2206&quot;&gt;COD 2&lt;/strong&gt; o &lt;strong data-end=&quot;2227&quot; data-start=&quot;2218&quot;&gt;COD 4&lt;/strong&gt;, tramite una comoda selezione della versione.&lt;br data-end=&quot;2276&quot; data-start=&quot;2273&quot; /&gt;\nNella vista elenco vengono mostrati dati essenziali come &lt;strong data-end=&quot;2352&quot; data-start=&quot;2333&quot;&gt;nome del server&lt;/strong&gt;, &lt;strong data-end=&quot;2368&quot; data-start=&quot;2354&quot;&gt;IP e porta&lt;/strong&gt;, &lt;strong data-end=&quot;2387&quot; data-start=&quot;2370&quot;&gt;mappa attuale&lt;/strong&gt;, &lt;strong data-end=&quot;2412&quot; data-start=&quot;2389&quot;&gt;numero di giocatori&lt;/strong&gt; e &lt;strong data-end=&quot;2435&quot; data-start=&quot;2415&quot;&gt;stato del server&lt;/strong&gt;. Ogni server può essere &lt;strong data-end=&quot;2471&quot; data-start=&quot;2460&quot;&gt;espanso&lt;/strong&gt; per visualizzare una &lt;strong data-end=&quot;2514&quot; data-start=&quot;2493&quot;&gt;vista dettagliata&lt;/strong&gt; con ulteriori informazioni, tra cui l’anteprima della mappa, la modalità di gioco e una &lt;strong data-end=&quot;2662&quot; data-start=&quot;2603&quot;&gt;lista dei giocatori in tempo reale con punteggio e ping&lt;/strong&gt;.&lt;br data-end=&quot;2666&quot; data-start=&quot;2663&quot; /&gt;\nLa Masterlist è ideale per clan, community e siti gaming che desiderano mostrare dati dei server sempre aggiornati e offrire ai giocatori una rapida panoramica dei server attivi.', '1.0.3', 'nexpell Team', 'https://www.nexpell.de', 'masterlist', '2026-03-15 15:05:07'),
(48, 'Pricing', 'pricing', 'Multilingual pricing pages with admin management.', '1.0.3', 'nexpell-team', 'https://www.nexpell.de', 'pricing', '2026-03-15 15:05:40'),
(49, 'Raidplaner', 'raidplaner', 'The raid planner is a versatile plugin for organizing and managing guild raids.', '1.0.3', 'Fjolnd', 'https://www.nexpell.de', 'raidplaner', '2026-03-15 15:22:00'),
(50, 'Forum', 'forum', '[[lang:de]]Mit dem Forum-Plugin kannst du ein modernes Forum direkt in deine Nexpell-Website integrieren und Besuchern eine zentrale Plattform für Diskussionen und Austausch bieten. Die erste Version konzentriert sich auf die grundlegenden Funktionen, um schnell und unkompliziert eine funktionierende Community aufzubauen.&lt;br /&gt;\nrnrnThemen und Beiträge lassen sich übersichtlich erstellen und lesen, sodass Nutzer sich aktiv beteiligen können. Das Forum eignet sich ideal für Community-Projekte, Support-Bereiche oder Diskussionen rund um deine Website und bildet eine solide Basis für zukünftige Erweiterungen.[[lang:en]]The Forum plugin allows you to integrate a modern forum directly into your Nexpell website, providing visitors with a central platform for discussions and exchange. The first version focuses on essential features to quickly set up a simple and functional community.&lt;br /&gt;\nrnrnTopics and posts can be created and read easily, encouraging users to participate in conversations. This forum is ideal for community projects, support sections, or discussions related to your website and serves as a solid foundation for future enhancements.[[lang:it]]Il plugin Forum consente di integrare un forum moderno direttamente nel tuo sito Nexpell, offrendo ai visitatori una piattaforma centrale per discussioni e scambio di opinioni. La prima versione si concentra sulle funzionalità di base per creare rapidamente una community semplice e funzionante.&lt;br /&gt;\nrnrnArgomenti e messaggi possono essere creati e letti facilmente, incoraggiando la partecipazione degli utenti. Il forum è ideale per progetti community, aree di supporto o discussioni legate al tuo sito web e rappresenta una solida base per future estensioni.', '1.0.3', 'nexpell-team', 'https://www.nexpell.de', 'forum', '2026-03-15 15:19:56'),
(51, 'Suche', 'search', 'With this plugin you can display the search on your website.', '1.0.3', 'nexpell-team', 'https://www.nexpell.de', 'search', '2026-03-15 15:06:06'),
(52, 'Links', 'links', 'With this plugin you can display your links.', '1.0.3', 'nexpell-team', 'https://www.nexpell.de', 'links', '2026-03-15 15:04:53');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `settings_plugins_lang`
--

CREATE TABLE `settings_plugins_lang` (
  `id` int(11) NOT NULL,
  `content_key` varchar(120) NOT NULL,
  `language` char(2) NOT NULL,
  `content` mediumtext NOT NULL,
  `modulname` varchar(255) NOT NULL DEFAULT '',
  `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
  `name` varchar(255) NOT NULL DEFAULT '',
  `lang` varchar(10) NOT NULL DEFAULT 'de',
  `translation` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `settings_plugins_lang`
--

INSERT INTO `settings_plugins_lang` (`id`, `content_key`, `language`, `content`, `modulname`, `updated_at`, `name`, `lang`, `translation`) VALUES
(1, 'plugin_name_startpage', 'de', 'Startpage', 'startpage', '2026-03-02 16:54:08', '', 'de', NULL),
(2, 'plugin_info_startpage', 'de', 'Kein Plugin. Bestandteil vom System!!!', 'startpage', '2026-03-02 16:54:08', '', 'de', NULL),
(3, 'plugin_info_startpage', 'en', 'No plugin. Part of the system!!!', 'startpage', '2026-03-02 16:54:08', '', 'de', NULL),
(4, 'plugin_info_startpage', 'it', 'Nessun plug-in. Parte del sistema!!!', 'startpage', '2026-03-02 16:54:08', '', 'de', NULL),
(5, 'plugin_name_privacy_policy', 'de', 'Privacy Policy', 'privacy_policy', '2026-03-02 16:54:08', '', 'de', NULL),
(6, 'plugin_info_privacy_policy', 'de', 'Kein Plugin. Bestandteil vom System!!!', 'privacy_policy', '2026-03-02 16:54:08', '', 'de', NULL),
(7, 'plugin_info_privacy_policy', 'en', 'No plugin. Part of the system!!!', 'privacy_policy', '2026-03-02 16:54:08', '', 'de', NULL),
(8, 'plugin_info_privacy_policy', 'it', 'Nessun plug-in. Parte del sistema!!!', 'privacy_policy', '2026-03-02 16:54:08', '', 'de', NULL),
(9, 'plugin_name_imprint', 'de', 'Imprint', 'imprint', '2026-03-02 16:54:08', '', 'de', NULL),
(10, 'plugin_info_imprint', 'de', 'Kein Plugin. Bestandteil vom System!!!', 'imprint', '2026-03-02 16:54:08', '', 'de', NULL),
(11, 'plugin_info_imprint', 'en', 'No plugin. Part of the system!!!', 'imprint', '2026-03-02 16:54:08', '', 'de', NULL),
(12, 'plugin_info_imprint', 'it', 'Nessun plug-in. Parte del sistema!!!', 'imprint', '2026-03-02 16:54:08', '', 'de', NULL),
(13, 'plugin_name_static', 'de', 'Static', 'static', '2026-03-02 16:54:08', '', 'de', NULL),
(14, 'plugin_info_static', 'de', 'Kein Plugin. Bestandteil vom System!!!', 'static', '2026-03-02 16:54:08', '', 'de', NULL),
(15, 'plugin_info_static', 'en', 'No plugin. Part of the system!!!', 'static', '2026-03-02 16:54:08', '', 'de', NULL),
(16, 'plugin_info_static', 'it', 'Nessun plug-in. Parte del sistema!!!', 'static', '2026-03-02 16:54:08', '', 'de', NULL),
(17, 'plugin_name_error_404', 'de', 'Error_404', 'error_404', '2026-03-02 16:54:08', '', 'de', NULL),
(18, 'plugin_info_error_404', 'de', 'Kein Plugin. Bestandteil vom System!!!', 'error_404', '2026-03-02 16:54:08', '', 'de', NULL),
(19, 'plugin_info_error_404', 'en', 'No plugin. Part of the system!!!', 'error_404', '2026-03-02 16:54:08', '', 'de', NULL),
(20, 'plugin_info_error_404', 'it', 'Nessun plug-in. Parte del sistema!!!', 'error_404', '2026-03-02 16:54:08', '', 'de', NULL),
(21, 'plugin_name_profile', 'de', 'Profile', 'profile', '2026-03-02 16:54:08', '', 'de', NULL),
(22, 'plugin_info_profile', 'de', 'Kein Plugin. Bestandteil vom System!!!', 'profile', '2026-03-02 16:54:08', '', 'de', NULL),
(23, 'plugin_info_profile', 'en', 'No plugin. Part of the system!!!', 'profile', '2026-03-02 16:54:08', '', 'de', NULL),
(24, 'plugin_info_profile', 'it', 'Nessun plug-in. Parte del sistema!!!', 'profile', '2026-03-02 16:54:08', '', 'de', NULL),
(25, 'plugin_name_login', 'de', 'Login', 'login', '2026-03-02 16:54:08', '', 'de', NULL),
(26, 'plugin_info_login', 'de', 'Kein Plugin. Bestandteil vom System!!!', 'login', '2026-03-02 16:54:08', '', 'de', NULL),
(27, 'plugin_info_login', 'en', 'No plugin. Part of the system!!!', 'login', '2026-03-02 16:54:08', '', 'de', NULL),
(28, 'plugin_info_login', 'it', 'Nessun plug-in. Parte del sistema!!!', 'login', '2026-03-02 16:54:08', '', 'de', NULL),
(29, 'plugin_name_lostpassword', 'de', 'Lost Password', 'lostpassword', '2026-03-02 16:54:08', '', 'de', NULL),
(30, 'plugin_info_lostpassword', 'de', 'Kein Plugin. Bestandteil vom System!!!', 'lostpassword', '2026-03-02 16:54:08', '', 'de', NULL),
(31, 'plugin_info_lostpassword', 'en', 'No plugin. Part of the system!!!', 'lostpassword', '2026-03-02 16:54:08', '', 'de', NULL),
(32, 'plugin_info_lostpassword', 'it', 'Nessun plug-in. Parte del sistema!!!', 'lostpassword', '2026-03-02 16:54:08', '', 'de', NULL),
(33, 'plugin_name_contact', 'de', 'Contact', 'contact', '2026-03-02 16:54:08', '', 'de', NULL),
(34, 'plugin_info_contact', 'de', 'Kein Plugin. Bestandteil vom System!!!', 'contact', '2026-03-02 16:54:08', '', 'de', NULL),
(35, 'plugin_info_contact', 'en', 'No plugin. Part of the system!!!', 'contact', '2026-03-02 16:54:08', '', 'de', NULL),
(36, 'plugin_info_contact', 'it', 'Nessun plug-in. Parte del sistema!!!', 'contact', '2026-03-02 16:54:08', '', 'de', NULL),
(37, 'plugin_name_register', 'de', 'Register', 'register', '2026-03-02 16:54:08', '', 'de', NULL),
(38, 'plugin_info_register', 'de', 'Kein Plugin. Bestandteil vom System!!!', 'register', '2026-03-02 16:54:08', '', 'de', NULL),
(39, 'plugin_info_register', 'en', 'No plugin. Part of the system!!!', 'register', '2026-03-02 16:54:08', '', 'de', NULL),
(40, 'plugin_info_register', 'it', 'Nessun plug-in. Parte del sistema!!!', 'register', '2026-03-02 16:54:08', '', 'de', NULL),
(41, 'plugin_name_edit_profile', 'de', 'Edit Profile', 'edit_profile', '2026-03-02 16:54:08', '', 'de', NULL),
(42, 'plugin_info_edit_profile', 'de', 'Kein Plugin. Bestandteil vom System!!!', 'edit_profile', '2026-03-02 16:54:08', '', 'de', NULL),
(43, 'plugin_info_edit_profile', 'en', 'No plugin. Part of the system!!!', 'edit_profile', '2026-03-02 16:54:08', '', 'de', NULL),
(44, 'plugin_info_edit_profile', 'it', 'Nessun plug-in. Parte del sistema!!!', 'edit_profile', '2026-03-02 16:54:08', '', 'de', NULL),
(45, 'plugin_name_navigation', 'de', 'Navigation', 'navigation', '2026-03-02 16:54:08', '', 'de', NULL),
(46, 'plugin_info_navigation', 'de', 'Mit diesem Plugin könnt ihr euch die Navigation anzeigen lassen.', 'navigation', '2026-03-02 16:54:08', '', 'de', NULL),
(47, 'plugin_info_navigation', 'en', 'With this plugin you can display navigation.', 'navigation', '2026-03-02 16:54:08', '', 'de', NULL),
(48, 'plugin_info_navigation', 'it', 'Con questo plugin puoi visualizzare la Barra di navigazione predefinita.', 'navigation', '2026-03-02 16:54:08', '', 'de', NULL),
(53, 'plugin_name_rules', 'de', 'Regeln', '', '2026-03-02 19:46:05', '', 'de', NULL),
(54, 'plugin_name_rules', 'en', 'Rules', '', '2026-03-02 19:46:05', '', 'de', NULL),
(55, 'plugin_name_rules', 'it', 'Regole', '', '2026-03-02 19:46:05', '', 'de', NULL),
(56, 'plugin_info_rules', 'de', 'Mit diesem Plugin könnt ihr eure Regeln anzeigen lassen.', '', '2026-03-02 19:46:05', '', 'de', NULL),
(57, 'plugin_info_rules', 'en', 'With this plugin it is possible to show the rules on the website.', '', '2026-03-02 19:46:05', '', 'de', NULL),
(58, 'plugin_info_rules', 'it', 'Con questo plugin è possibile mostrare le regole sul sito web.', '', '2026-03-02 19:46:05', '', 'de', NULL),
(65, 'plugin_name_footer', 'de', 'Footer', 'footer_easy', '2026-03-15 14:11:36', '', 'de', NULL),
(66, 'plugin_info_footer', 'de', 'Mit diesem Plugin könnt ihr einen neuen Footer anzeigen lassen.', 'footer', '2026-03-15 14:11:36', '', 'de', NULL),
(67, 'plugin_info_footer', 'en', 'With this plugin you can have a new Footer displayed.', 'footer', '2026-03-15 14:11:36', '', 'de', NULL),
(68, 'plugin_info_footer', 'it', 'Con questo plugin puoi visualizzare un nuovo piè di pagina.', 'footer', '2026-03-15 14:11:36', '', 'de', NULL),
(81, 'plugin_name_about', 'de', 'Ueber uns', 'about', '2026-03-15 15:01:32', '', 'de', NULL),
(82, 'plugin_name_about', 'en', 'About Us', 'about', '2026-03-15 15:01:32', '', 'de', NULL),
(83, 'plugin_name_about', 'it', 'Chi siamo', 'about', '2026-03-15 15:01:32', '', 'de', NULL),
(84, 'plugin_info_about', 'de', 'Dieses Widget zeigt allgemeine Informationen ...', 'about', '2026-03-15 15:01:32', '', 'de', NULL),
(85, 'plugin_info_about', 'en', 'This widget shows general information ...', 'about', '2026-03-15 15:01:32', '', 'de', NULL),
(86, 'plugin_info_about', 'it', 'Questo widget mostra informazioni generali ...', 'about', '2026-03-15 15:01:32', '', 'de', NULL),
(87, 'plugin_name_achievements', 'de', 'Achievements', '', '2026-03-04 20:21:11', '', 'de', NULL),
(88, 'plugin_name_achievements', 'en', 'Achievements', '', '2026-03-04 20:21:11', '', 'de', NULL),
(89, 'plugin_name_achievements', 'it', 'Achievements', '', '2026-03-04 20:21:11', '', 'de', NULL),
(90, 'plugin_info_achievements', 'de', 'Das Achievements Plugin erweitert Nexpell um ein flexibles Belohnungs- und Auszeichnungssystem.\r\nBenutzer erhalten automatisch oder manuell vergebene Errungenschaften (Badges) für bestimmte Aktivitäten, Meilensteine oder besondere Leistungen.', '', '2026-03-04 20:21:11', '', 'de', NULL),
(91, 'plugin_info_achievements', 'en', 'The Achievements plugin extends Nexpell with a flexible reward and recognition system.\r\nUsers receive automatically or manually assigned achievements (badges) for specific activities, milestones, or special accomplishments.', '', '2026-03-04 20:21:11', '', 'de', NULL),
(92, 'plugin_info_achievements', 'it', 'Il plugin Achievements estende Nexpell con un sistema flessibile di premi e riconoscimenti.\r\nGli utenti ricevono automaticamente o manualmente risultati (badge) per attività specifiche, traguardi o prestazioni speciali.', '', '2026-03-04 20:21:11', '', 'de', NULL),
(99, 'plugin_name_carousel', 'de', 'Carousel', 'carousel', '2026-03-15 15:01:39', '', 'de', NULL),
(100, 'plugin_name_carousel', 'en', 'Carousel', 'carousel', '2026-03-15 15:01:39', '', 'de', NULL),
(101, 'plugin_name_carousel', 'it', 'Carousel', 'carousel', '2026-03-15 15:01:39', '', 'de', NULL),
(102, 'plugin_info_carousel', 'de', 'Mit diesem Plugin koennt ihr ein Carousel in die Webseite einbinden.', 'carousel', '2026-03-15 15:01:39', '', 'de', NULL),
(103, 'plugin_info_carousel', 'en', 'With this plugin you can integrate a carousel into your website.', 'carousel', '2026-03-15 15:01:39', '', 'de', NULL),
(104, 'plugin_info_carousel', 'it', 'Con questo plugin puoi integrare un carosello nel sito web.', 'carousel', '2026-03-15 15:01:39', '', 'de', NULL),
(105, 'plugin_name_userlist', 'de', 'Userlist', 'userlist', '2026-03-15 15:08:30', '', 'de', NULL),
(106, 'plugin_name_userlist', 'en', 'Userlist', 'userlist', '2026-03-15 15:08:30', '', 'de', NULL),
(107, 'plugin_name_userlist', 'it', 'Userlist', 'userlist', '2026-03-15 15:08:30', '', 'de', NULL),
(108, 'plugin_info_userlist', 'de', 'Mit diesem Plugin koennt ihr eure Registered Users anzeigen lassen.', 'userlist', '2026-03-15 15:08:30', '', 'de', NULL),
(109, 'plugin_info_userlist', 'en', 'With this plugin you can display your registered user.', 'userlist', '2026-03-15 15:08:30', '', 'de', NULL),
(110, 'plugin_info_userlist', 'it', 'Con questo plugin puoi visualizzare la lista dei tuoi utenti registrati.', 'userlist', '2026-03-15 15:08:30', '', 'de', NULL),
(123, 'plugin_name_counter', 'de', 'Counter', 'counter', '2026-03-15 15:01:42', '', 'de', NULL),
(124, 'plugin_name_counter', 'en', 'Counter', 'counter', '2026-03-15 15:01:42', '', 'de', NULL),
(125, 'plugin_name_counter', 'it', 'Counter', 'counter', '2026-03-15 15:01:42', '', 'de', NULL),
(126, 'plugin_info_counter', 'de', 'Mit diesem Plugin koennt ihr euren Counter und Besucherstatistiken anzeigen lassen.', 'counter', '2026-03-15 15:01:42', '', 'de', NULL),
(127, 'plugin_info_counter', 'en', 'With this plugin you can display your counter and visitor statistics.', 'counter', '2026-03-15 15:01:42', '', 'de', NULL),
(128, 'plugin_info_counter', 'it', 'Con questo plugin puoi visualizzare il contatore e le statistiche dei visitatori.', 'counter', '2026-03-15 15:01:42', '', 'de', NULL),
(129, 'plugin_name_partners', 'de', 'Partner', 'partners', '2026-03-15 15:05:33', '', 'de', NULL),
(130, 'plugin_name_partners', 'en', 'Partners', 'partners', '2026-03-15 15:05:33', '', 'de', NULL),
(131, 'plugin_name_partners', 'it', 'Partner', 'partners', '2026-03-15 15:05:33', '', 'de', NULL),
(132, 'plugin_info_partners', 'de', 'Mit diesem Plugin koennt ihr eure Partner mit Slider und Page anzeigen lassen.', 'partners', '2026-03-15 15:05:33', '', 'de', NULL),
(133, 'plugin_info_partners', 'en', 'With this plugin you can display your partners with slider and page.', 'partners', '2026-03-15 15:05:33', '', 'de', NULL),
(134, 'plugin_info_partners', 'it', 'Con questo plugin puoi visualizzare i tuoi partner con slider e pagina.', 'partners', '2026-03-15 15:05:33', '', 'de', NULL),
(135, 'plugin_name_articles', 'de', 'Articles', 'articles', '2026-03-15 15:01:35', '', 'de', NULL),
(136, 'plugin_name_articles', 'en', 'Articles', 'articles', '2026-03-15 15:01:35', '', 'de', NULL),
(137, 'plugin_name_articles', 'it', 'Articles', 'articles', '2026-03-15 15:01:35', '', 'de', NULL),
(138, 'plugin_info_articles', 'de', 'Mit diesem Plugin koennt ihr eure Articles anzeigen lassen.', 'articles', '2026-03-15 15:01:35', '', 'de', NULL),
(139, 'plugin_info_articles', 'en', 'With this plugin you can display your articles.', 'articles', '2026-03-15 15:01:35', '', 'de', NULL),
(140, 'plugin_info_articles', 'it', 'Con questo plugin e possibile mostrare gli Articoli sul sito web.', 'articles', '2026-03-15 15:01:35', '', 'de', NULL),
(141, 'plugin_name_discord', 'de', 'Discord', '', '2026-03-05 20:58:55', '', 'de', NULL),
(142, 'plugin_name_discord', 'en', 'Discord', '', '2026-03-05 20:58:55', '', 'de', NULL),
(143, 'plugin_name_discord', 'it', 'Discord', '', '2026-03-05 20:58:55', '', 'de', NULL),
(144, 'plugin_info_discord', 'de', 'Dieses Widget zeigt die Entwicklungsgeschichte und wichtige Meilensteine von nexpell auf Ihrer Webseite an.', '', '2026-03-05 20:58:55', '', 'de', NULL),
(145, 'plugin_info_discord', 'en', 'This widget displays the development history and key milestones of nexpell on your website.', '', '2026-03-05 20:58:55', '', 'de', NULL),
(146, 'plugin_info_discord', 'it', 'Questo widget mostra la storia dello sviluppo e le tappe fondamentali di nexpell sul tuo sito web.', '', '2026-03-05 20:58:55', '', 'de', NULL),
(147, 'plugin_name_downloads', 'de', 'Download', 'downloads', '2026-03-15 15:01:48', '', 'de', NULL),
(148, 'plugin_name_downloads', 'en', 'Download', 'downloads', '2026-03-15 15:01:48', '', 'de', NULL),
(149, 'plugin_name_downloads', 'it', 'Download', 'downloads', '2026-03-15 15:01:48', '', 'de', NULL),
(150, 'plugin_info_downloads', 'de', 'Mit diesem Plugin koennt ihr eure Downloads anzeigen lassen.', 'downloads', '2026-03-15 15:01:48', '', 'de', NULL),
(151, 'plugin_info_downloads', 'en', 'With this plugin you can display your downloads.', 'downloads', '2026-03-15 15:01:48', '', 'de', NULL),
(152, 'plugin_info_downloads', 'it', 'Con questo plugin e possibile mostrare i download sul sito web.', 'downloads', '2026-03-15 15:01:48', '', 'de', NULL),
(153, 'plugin_name_twitch', 'de', 'Twitch', '', '2026-03-06 20:56:42', '', 'de', NULL),
(154, 'plugin_name_twitch', 'en', 'Twitch', '', '2026-03-06 20:56:42', '', 'de', NULL),
(155, 'plugin_name_twitch', 'it', 'Twitch', '', '2026-03-06 20:56:42', '', 'de', NULL),
(156, 'plugin_info_twitch', 'de', 'Dieses Widget zeigt die Entwicklungsgeschichte und wichtige Meilensteine von nexpell auf Ihrer Webseite an.', '', '2026-03-06 20:56:42', '', 'de', NULL),
(157, 'plugin_info_twitch', 'en', 'This widget displays the development history and key milestones of nexpell on your website.', '', '2026-03-06 20:56:42', '', 'de', NULL),
(158, 'plugin_info_twitch', 'it', 'Questo widget mostra la storia dello sviluppo e le tappe fondamentali di nexpell sul tuo sito web.', '', '2026-03-06 20:56:42', '', 'de', NULL),
(159, 'plugin_name_teamspeak', 'de', 'TeamSpeak', '', '2026-03-07 00:00:48', '', 'de', NULL),
(160, 'plugin_name_teamspeak', 'en', 'TeamSpeak', '', '2026-03-07 00:00:48', '', 'de', NULL),
(161, 'plugin_name_teamspeak', 'it', 'TeamSpeak', '', '2026-03-07 00:00:48', '', 'de', NULL),
(162, 'plugin_info_teamspeak', 'de', 'Zeigt einen TeamSpeak-Server mit Channels und Usern an.', '', '2026-03-07 00:00:48', '', 'de', NULL),
(163, 'plugin_info_teamspeak', 'en', 'Displays a TeamSpeak server with channels and users.', '', '2026-03-07 00:00:48', '', 'de', NULL),
(164, 'plugin_info_teamspeak', 'it', 'Mostra un server TeamSpeak con canali e utenti.', '', '2026-03-07 00:00:48', '', 'de', NULL),
(183, 'plugin_name_gametracker', 'de', 'Gametracker', 'gametracker', '2026-03-15 15:02:28', '', 'de', NULL),
(184, 'plugin_name_gametracker', 'en', 'Gametracker', 'gametracker', '2026-03-15 15:02:28', '', 'de', NULL),
(185, 'plugin_name_gametracker', 'it', 'Gametracker', 'gametracker', '2026-03-15 15:02:28', '', 'de', NULL),
(186, 'plugin_info_gametracker', 'de', 'Zeigt Informationen zu deinen Spielservern wie Name, Karte, Spieleranzahl und Status direkt auf deiner Website an.', 'gametracker', '2026-03-15 15:02:28', '', 'de', NULL),
(187, 'plugin_info_gametracker', 'en', 'Displays information about your game servers such as name, map, player count, and status directly on your website.', 'gametracker', '2026-03-15 15:02:28', '', 'de', NULL),
(188, 'plugin_info_gametracker', 'it', 'Mostra le informazioni dei tuoi server di gioco come nome, mappa, numero di giocatori e stato direttamente sul tuo sito web.', 'gametracker', '2026-03-15 15:02:28', '', 'de', NULL),
(189, 'plugin_name_youtube', 'de', 'Youtube', 'youtube', '2026-03-15 15:08:37', '', 'de', NULL),
(190, 'plugin_name_youtube', 'en', 'Youtube', 'youtube', '2026-03-15 15:08:37', '', 'de', NULL),
(191, 'plugin_name_youtube', 'it', 'Youtube', 'youtube', '2026-03-15 15:08:37', '', 'de', NULL),
(192, 'plugin_info_youtube', 'de', 'Mit diesem Plugin koennt ihr eure Youtube-Videos anzeigen lassen.', 'youtube', '2026-03-15 15:08:37', '', 'de', NULL),
(193, 'plugin_info_youtube', 'en', 'With this plugin you can display your Youtube videos.', 'youtube', '2026-03-15 15:08:37', '', 'de', NULL),
(194, 'plugin_info_youtube', 'it', 'Con questo plugin puoi visualizzare i tuoi video Youtube sul sito web.', 'youtube', '2026-03-15 15:08:37', '', 'de', NULL),
(195, 'plugin_name_todo', 'de', 'Todo', '', '2026-03-07 18:10:09', '', 'de', NULL),
(196, 'plugin_name_todo', 'en', 'Todo', '', '2026-03-07 18:10:09', '', 'de', NULL),
(197, 'plugin_name_todo', 'it', 'Todo', '', '2026-03-07 18:10:09', '', 'de', NULL),
(198, 'plugin_info_todo', 'de', 'Dieses Widget zeigt allgemeine Informationen (kleiner Lebenslauf) über Sie auf Ihrer Webspell-RM-RM-Seite an.', '', '2026-03-07 18:10:09', '', 'de', NULL),
(199, 'plugin_info_todo', 'en', 'This widget will show general information (small resume) todo You on your Webspell-RM-RM site.', '', '2026-03-07 18:10:09', '', 'de', NULL),
(200, 'plugin_info_todo', 'it', 'Questo widget mostrerà informazioni generali (piccolo curriculum) su di te sul tuo sito Webspell-RM-RM.', '', '2026-03-07 18:10:09', '', 'de', NULL),
(201, 'plugin_name_sponsors', 'de', 'Sponsors', 'sponsors', '2026-03-15 15:08:06', '', 'de', NULL),
(202, 'plugin_name_sponsors', 'en', 'Sponsors', 'sponsors', '2026-03-15 15:08:06', '', 'de', NULL),
(203, 'plugin_name_sponsors', 'it', 'Sponsors', 'sponsors', '2026-03-15 15:08:06', '', 'de', NULL),
(204, 'plugin_info_sponsors', 'de', 'Mit diesem Plugin koennt ihr eure Sponsoren anzeigen lassen.', 'sponsors', '2026-03-15 15:08:06', '', 'de', NULL),
(205, 'plugin_info_sponsors', 'en', 'With this plugin you can display your sponsors.', 'sponsors', '2026-03-15 15:08:06', '', 'de', NULL),
(206, 'plugin_info_sponsors', 'it', 'Con questo plugin puoi visualizzare i tuoi sponsor.', 'sponsors', '2026-03-15 15:08:06', '', 'de', NULL),
(207, 'sponsors_headline', 'en', 'Our Sponsors & Partners', 'sponsors', '2026-03-15 15:08:06', '', 'de', NULL),
(208, 'sponsors_headline', 'de', 'Unsere Sponsoren & Partner', 'sponsors', '2026-03-15 15:08:06', '', 'de', NULL),
(209, 'sponsors_headline', 'it', 'I nostri sponsor e partner', 'sponsors', '2026-03-15 15:08:06', '', 'de', NULL),
(210, 'sponsors_intro', 'en', 'Our sponsors and partners support nexpell as a modern, modular content management system for clans, clubs, and projects. They help us continuously develop new features and keep the software free and open for everyone. Thank you for your valuable support!', 'sponsors', '2026-03-15 15:08:06', '', 'de', NULL),
(211, 'sponsors_intro', 'de', 'Unsere Sponsoren und Partner unterstuetzen nexpell als modernes, modulares Content-Management-System fuer Clans, Vereine und Projekte. Sie tragen dazu bei, dass wir kontinuierlich neue Features entwickeln und die Software frei und offen fuer alle bereitstellen koennen. Vielen Dank fuer eure wertvolle Unterstuetzung!', 'sponsors', '2026-03-15 15:08:06', '', 'de', NULL),
(212, 'sponsors_intro', 'it', 'I nostri sponsor e partner supportano nexpell come sistema di gestione dei contenuti moderno e modulare per clan, associazioni e progetti. Ci aiutano a sviluppare continuamente nuove funzionalita e a mantenere il software libero e aperto a tutti. Grazie per il vostro prezioso supporto!', 'sponsors', '2026-03-15 15:08:06', '', 'de', NULL),
(225, 'nav_website_about_1', 'en', 'About Us', '', '2026-03-07 22:23:49', '', 'de', NULL),
(226, 'nav_website_about_1', 'de', 'Über uns', '', '2026-03-07 22:23:49', '', 'de', NULL),
(227, 'nav_website_about_1', 'it', 'Chi siamo', '', '2026-03-07 22:23:49', '', 'de', NULL),
(228, 'nav_website_about_2', 'de', 'Info', '', '2026-03-07 22:23:49', '', 'de', NULL),
(229, 'nav_website_about_3', 'de', 'Leistung', '', '2026-03-07 22:23:49', '', 'de', NULL),
(385, 'plugin_name_lastlogin', 'de', 'Lastlogin', 'lastlogin', '2026-03-15 15:02:33', '', 'de', NULL),
(386, 'plugin_name_lastlogin', 'en', 'Lastlogin', 'lastlogin', '2026-03-15 15:02:33', '', 'de', NULL),
(387, 'plugin_name_lastlogin', 'it', 'Lastlogin', 'lastlogin', '2026-03-15 15:02:33', '', 'de', NULL),
(388, 'plugin_info_lastlogin', 'de', 'Mit diesem Plugin ist es moeglich die Aktivitaet der User und Mitglieder zu ueberpruefen.', 'lastlogin', '2026-03-15 15:02:33', '', 'de', NULL),
(389, 'plugin_info_lastlogin', 'en', 'With this plugin it is possible to check the activity of the users and members.', 'lastlogin', '2026-03-15 15:02:33', '', 'de', NULL),
(390, 'plugin_info_lastlogin', 'it', 'Con questo plugin e possibile controllare l\'attivita degli utenti e dei membri.', 'lastlogin', '2026-03-15 15:02:33', '', 'de', NULL),
(391, 'plugin_name_messenger', 'de', 'Messenger', 'messenger', '2026-03-15 15:05:15', '', 'de', NULL),
(392, 'plugin_name_messenger', 'en', 'Messenger', 'messenger', '2026-03-15 15:05:15', '', 'de', NULL),
(393, 'plugin_name_messenger', 'it', 'Messenger', 'messenger', '2026-03-15 15:05:15', '', 'de', NULL),
(394, 'plugin_info_messenger', 'de', 'Mit diesem Plugin kannst du private Nachrichten zwischen Benutzern senden und empfangen.', 'messenger', '2026-03-15 15:05:15', '', 'de', NULL),
(395, 'plugin_info_messenger', 'en', 'With this plugin you can send and receive private messages between users.', 'messenger', '2026-03-15 15:05:15', '', 'de', NULL),
(396, 'plugin_info_messenger', 'it', 'Con questo plugin puoi inviare e ricevere messaggi privati tra gli utenti.', 'messenger', '2026-03-15 15:05:15', '', 'de', NULL),
(397, 'plugin_name_news', 'de', 'News', 'news', '2026-03-15 15:05:26', '', 'de', NULL),
(398, 'plugin_name_news', 'en', 'News', 'news', '2026-03-15 15:05:26', '', 'de', NULL),
(399, 'plugin_name_news', 'it', 'News', 'news', '2026-03-15 15:05:26', '', 'de', NULL),
(400, 'plugin_info_news', 'de', 'Dieses Plugin ermoeglicht das Erstellen und Verwalten von News-Artikeln auf Ihrer Webspell-RM-Seite.', 'news', '2026-03-15 15:05:26', '', 'de', NULL),
(401, 'plugin_info_news', 'en', 'This plugin allows you to create and manage news articles on your Webspell-RM site.', 'news', '2026-03-15 15:05:26', '', 'de', NULL),
(402, 'plugin_info_news', 'it', 'Questo plugin consente di creare e gestire articoli di notizie sul tuo sito Webspell-RM.', 'news', '2026-03-15 15:05:26', '', 'de', NULL),
(403, 'plugin_name_shoutbox', 'de', 'Shoutbox', '', '2026-03-08 16:55:53', '', 'de', NULL),
(404, 'plugin_name_shoutbox', 'en', 'Shoutbox', '', '2026-03-08 16:55:53', '', 'de', NULL),
(405, 'plugin_name_shoutbox', 'it', 'Shoutbox', '', '2026-03-08 16:55:53', '', 'de', NULL),
(406, 'plugin_info_shoutbox', 'de', 'Mit diesem Plugin könnt ihr ein shoutbox auf die Webseite anzeigen lassen.', '', '2026-03-08 16:55:53', '', 'de', NULL),
(407, 'plugin_info_shoutbox', 'en', 'With this plugin you can display a shoutbox on the website.', '', '2026-03-08 16:55:53', '', 'de', NULL),
(408, 'plugin_info_shoutbox', 'it', 'Con questo plugin puoi visualizzare una galleria sul sito web.', '', '2026-03-08 16:55:53', '', 'de', NULL),
(409, 'plugin_name_live_visitor', 'de', 'Live Visitor', 'live_visitor', '2026-03-15 15:04:59', '', 'de', NULL),
(410, 'plugin_name_live_visitor', 'en', 'Live Visitor', 'live_visitor', '2026-03-15 15:04:59', '', 'de', NULL),
(411, 'plugin_name_live_visitor', 'it', 'Live Visitor', 'live_visitor', '2026-03-15 15:04:59', '', 'de', NULL),
(412, 'plugin_info_live_visitor', 'de', 'Mit diesem Plugin koennt ihr eure Live-Besucher, Who is Online und Besucherstatistiken anzeigen lassen.', 'live_visitor', '2026-03-15 15:04:59', '', 'de', NULL),
(413, 'plugin_info_live_visitor', 'en', 'With this plugin you can display your live visitors, Who is Online and visitor statistics.', 'live_visitor', '2026-03-15 15:04:59', '', 'de', NULL),
(414, 'plugin_info_live_visitor', 'it', 'Con questo plugin puoi visualizzare i visitatori in tempo reale, Who is Online e le statistiche dei visitatori.', 'live_visitor', '2026-03-15 15:04:59', '', 'de', NULL),
(415, 'plugin_name_gallery', 'de', 'Gallery', 'gallery', '2026-03-15 15:02:22', '', 'de', NULL),
(416, 'plugin_name_gallery', 'en', 'Gallery', 'gallery', '2026-03-15 15:02:22', '', 'de', NULL),
(417, 'plugin_name_gallery', 'it', 'Gallery', 'gallery', '2026-03-15 15:02:22', '', 'de', NULL),
(418, 'plugin_info_gallery', 'de', 'Mit diesem Plugin koennt ihr eine Gallery auf der Webseite anzeigen lassen.', 'gallery', '2026-03-15 15:02:22', '', 'de', NULL),
(419, 'plugin_info_gallery', 'en', 'With this plugin you can display a gallery on the website.', 'gallery', '2026-03-15 15:02:22', '', 'de', NULL),
(420, 'plugin_info_gallery', 'it', 'Con questo plugin puoi visualizzare una galleria sul sito web.', 'gallery', '2026-03-15 15:02:22', '', 'de', NULL),
(421, 'plugin_name_joinus', 'de', 'JoinUs', '', '2026-03-09 20:59:50', '', 'de', NULL),
(422, 'plugin_name_joinus', 'en', 'JoinUs', '', '2026-03-09 20:59:50', '', 'de', NULL),
(423, 'plugin_name_joinus', 'it', 'JoinUs', '', '2026-03-09 20:59:50', '', 'de', NULL),
(424, 'plugin_info_joinus', 'de', 'JoinUs Bewerbungsformular', '', '2026-03-09 20:59:50', '', 'de', NULL),
(425, 'plugin_info_joinus', 'en', 'JoinUs application form', '', '2026-03-09 20:59:50', '', 'de', NULL),
(426, 'plugin_info_joinus', 'it', 'JoinUs application form', '', '2026-03-09 20:59:50', '', 'de', NULL),
(427, 'plugin_name_masterlist', 'de', 'Masterlist', '', '2026-03-10 19:26:00', '', 'de', NULL),
(428, 'plugin_name_masterlist', 'en', 'Masterlist', '', '2026-03-10 19:26:00', '', 'de', NULL),
(429, 'plugin_name_masterlist', 'it', 'Masterlist', '', '2026-03-10 19:26:00', '', 'de', NULL),
(430, 'plugin_info_masterlist', 'de', 'Mit diesem Plugin k�nnt ihr eure masterlist mit Slider und Page anzeigen lassen.', '', '2026-03-10 19:26:00', '', 'de', NULL),
(431, 'plugin_info_masterlist', 'en', 'With this plugin you can display your masterlist with slider and page.', '', '2026-03-10 19:26:00', '', 'de', NULL),
(432, 'plugin_info_masterlist', 'it', 'Con questo plugin puoi visualizzare i tuoi masterlist con slider e pagina.', '', '2026-03-10 19:26:00', '', 'de', NULL),
(433, 'plugin_name_pricing', 'de', 'Pricing', 'pricing', '2026-03-15 15:05:40', '', 'de', NULL),
(434, 'plugin_name_pricing', 'en', 'Pricing', 'pricing', '2026-03-15 15:05:40', '', 'de', NULL),
(435, 'plugin_name_pricing', 'it', 'Pricing', 'pricing', '2026-03-15 15:05:40', '', 'de', NULL),
(436, 'plugin_info_pricing', 'de', 'Mehrsprachige Pricing-Seiten mit Adminverwaltung.', 'pricing', '2026-03-15 15:05:40', '', 'de', NULL),
(437, 'plugin_info_pricing', 'en', 'Multilingual pricing pages with admin management.', 'pricing', '2026-03-15 15:05:40', '', 'de', NULL),
(438, 'plugin_info_pricing', 'it', 'Pagine pricing multilingua con gestione admin.', 'pricing', '2026-03-15 15:05:40', '', 'de', NULL),
(439, 'plugin_name_raidplaner', 'de', 'Raidplaner', 'raidplaner', '2026-03-15 15:22:00', '', 'de', NULL),
(440, 'plugin_name_raidplaner', 'en', 'Raidplaner', 'raidplaner', '2026-03-15 15:22:00', '', 'de', NULL),
(441, 'plugin_name_raidplaner', 'it', 'Raidplaner', 'raidplaner', '2026-03-15 15:22:00', '', 'de', NULL),
(442, 'plugin_info_raidplaner', 'de', 'Der Raidplaner ist ein vielseitiges Plugin zur Organisation und Verwaltung von Gilden-Raids. Es ermoeglicht das Erstellen von Raids mit Bossen, Rollenverteilung und Teilnehmeranmeldung inklusive Charakter- und Klassenverwaltung. Loot-, Anwesenheits- und BiS-Tracking unterstuetzen die Auswertung und Itemplanung. Ueber den Adminbereich koennen Bosse, Templates und Einstellungen komfortabel gepflegt werden. Optional ist eine Discord-Anbindung fuer automatische Raidankuendigungen integriert.', 'raidplaner', '2026-03-15 15:22:00', '', 'de', NULL),
(443, 'plugin_info_raidplaner', 'en', 'The raid planner is a versatile plugin for organizing and managing guild raids. It allows you to create raids with bosses, assign roles, and handle participant sign-ups, including character and class management. Loot, attendance, and BiS tracking support analysis and item planning. Through the admin area, bosses, templates, and settings can be conveniently maintained. An optional Discord integration enables automatic raid announcements.', 'raidplaner', '2026-03-15 15:22:00', '', 'de', NULL),
(444, 'plugin_info_raidplaner', 'it', 'The raid planner is a versatile plugin for organizing and managing guild raids. It allows you to create raids with bosses, assign roles, and handle participant sign-ups, including character and class management. Loot, attendance, and BiS tracking support analysis and item planning. Through the admin area, bosses, templates, and settings can be conveniently maintained. An optional Discord integration enables automatic raid announcements.', 'raidplaner', '2026-03-15 15:22:00', '', 'de', NULL),
(445, 'plugin_name_forum', 'de', 'Forum', '', '2026-03-11 20:27:22', '', 'de', NULL),
(446, 'plugin_name_forum', 'en', 'Forum', '', '2026-03-11 20:27:22', '', 'de', NULL),
(447, 'plugin_name_forum', 'it', 'Forum', '', '2026-03-11 20:27:22', '', 'de', NULL),
(448, 'plugin_info_forum', 'de', 'Forum Plugin für Diskussionen', '', '2026-03-11 20:27:22', '', 'de', NULL),
(449, 'plugin_info_forum', 'en', 'Forum plugin for discussions', '', '2026-03-11 20:27:22', '', 'de', NULL),
(450, 'plugin_info_forum', 'it', 'Plugin forum per discussioni', '', '2026-03-11 20:27:22', '', 'de', NULL),
(451, 'plugin_name_search', 'de', 'Search', 'search', '2026-03-15 15:06:06', '', 'de', NULL),
(452, 'plugin_name_search', 'en', 'Search', 'search', '2026-03-15 15:06:06', '', 'de', NULL),
(453, 'plugin_name_search', 'it', 'Search', 'search', '2026-03-15 15:06:06', '', 'de', NULL),
(454, 'plugin_info_search', 'de', 'Mit diesem Plugin koennt ihr eure Suche auf der Website anzeigen lassen.', 'search', '2026-03-15 15:06:06', '', 'de', NULL),
(455, 'plugin_info_search', 'en', 'With this plugin you can display the search on your website.', 'search', '2026-03-15 15:06:06', '', 'de', NULL),
(456, 'plugin_info_search', 'it', 'Con questo plugin potete mostrare la funzione di ricerca sul vostro sito web.', 'search', '2026-03-15 15:06:06', '', 'de', NULL),
(457, 'plugin_name_links', 'de', 'Links', 'links', '2026-03-15 15:04:53', '', 'de', NULL),
(458, 'plugin_name_links', 'en', 'Links', 'links', '2026-03-15 15:04:53', '', 'de', NULL),
(459, 'plugin_name_links', 'it', 'Link', 'links', '2026-03-15 15:04:53', '', 'de', NULL),
(460, 'plugin_info_links', 'de', 'Mit diesem Plugin koennt ihr eure Links anzeigen lassen.', 'links', '2026-03-15 15:04:53', '', 'de', NULL),
(461, 'plugin_info_links', 'en', 'With this plugin you can display your links.', 'links', '2026-03-15 15:04:53', '', 'de', NULL),
(462, 'plugin_info_links', 'it', 'Con questo plugin puoi visualizzare i tuoi link.', 'links', '2026-03-15 15:04:53', '', 'de', NULL);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `settings_seo_meta_lang`
--

CREATE TABLE `settings_seo_meta_lang` (
  `id` int(11) NOT NULL,
  `content_key` varchar(191) NOT NULL,
  `language` varchar(8) NOT NULL DEFAULT 'de',
  `content` mediumtext NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `settings_seo_meta_lang`
--

INSERT INTO `settings_seo_meta_lang` (`id`, `content_key`, `language`, `content`, `updated_at`) VALUES
(1, 'seo_title_about', 'de', 'Über uns – Das Team hinter Nexpell', '2026-03-02 16:54:08'),
(2, 'seo_description_about', 'de', 'Lerne das Team und die Geschichte von Nexpell kennen. Ein modernes Open-Source-CMS für Gamer.', '2026-03-02 16:54:08'),
(3, 'seo_title_about', 'en', 'About Us – The Team Behind Nexpell', '2026-03-02 16:54:08'),
(4, 'seo_description_about', 'en', 'Get to know the team and story behind Nexpell. A modern open-source CMS for gamers.', '2026-03-02 16:54:08'),
(5, 'seo_title_about', 'it', 'Chi siamo – Il team dietro Nexpell', '2026-03-02 16:54:08'),
(6, 'seo_description_about', 'it', 'Scopri il team e la storia di Nexpell. Un CMS moderno e open-source per gamer.', '2026-03-02 16:54:08'),
(7, 'seo_title_achievements', 'de', 'Erfolge – Errungenschaften deiner Community', '2026-03-02 16:54:08'),
(8, 'seo_description_achievements', 'de', 'Zeige freigeschaltete Achievements und Fortschritte deiner Nutzer an und motiviere die Community.', '2026-03-02 16:54:08'),
(9, 'seo_title_achievements', 'en', 'Achievements – Community Rewards and Progress', '2026-03-02 16:54:08'),
(10, 'seo_description_achievements', 'en', 'Display unlocked achievements and user progress to motivate your community.', '2026-03-02 16:54:08'),
(11, 'seo_title_achievements', 'it', 'Obiettivi – Traguardi della tua community', '2026-03-02 16:54:08'),
(12, 'seo_description_achievements', 'it', 'Mostra gli obiettivi sbloccati e i progressi degli utenti per motivare la community.', '2026-03-02 16:54:08'),
(13, 'seo_title_articles', 'de', 'Artikel – Aktuelle Beiträge und News', '2026-03-02 16:54:08'),
(14, 'seo_description_articles', 'de', 'Entdecke spannende Artikel, Neuigkeiten und Hintergrundberichte rund um Nexpell und seine Community.', '2026-03-02 16:54:08'),
(15, 'seo_title_articles', 'en', 'Articles – Latest Posts and News', '2026-03-02 16:54:08'),
(16, 'seo_description_articles', 'en', 'Discover articles, news and in-depth reports about Nexpell and its community.', '2026-03-02 16:54:08'),
(17, 'seo_title_articles', 'it', 'Articoli – Ultimi post e notizie', '2026-03-02 16:54:08'),
(18, 'seo_description_articles', 'it', 'Scopri articoli, notizie e approfondimenti sulla community di Nexpell.', '2026-03-02 16:54:08'),
(19, 'seo_title_blog', 'de', 'Blog – Beiträge und Artikel auf deiner Website', '2026-03-02 16:54:08'),
(20, 'seo_description_blog', 'de', 'Erstelle persönliche oder themenbezogene Blogbeiträge und teile Neuigkeiten mit deiner Community.', '2026-03-02 16:54:08'),
(21, 'seo_title_blog', 'en', 'Blog – Posts and Articles for Your Website', '2026-03-02 16:54:08'),
(22, 'seo_description_blog', 'en', 'Create personal or thematic blog posts and share updates with your community.', '2026-03-02 16:54:08'),
(23, 'seo_title_blog', 'it', 'Blog – Articoli e post per il tuo sito', '2026-03-02 16:54:08'),
(24, 'seo_description_blog', 'it', 'Crea articoli personali o a tema e condividi aggiornamenti con la tua community.', '2026-03-02 16:54:08'),
(25, 'seo_title_carousel', 'de', 'Carousel – Slider für deine Startseite', '2026-03-02 16:54:08'),
(26, 'seo_description_carousel', 'de', 'Füge deiner Website einen modernen Bild- und Text-Slider hinzu.', '2026-03-02 16:54:08'),
(27, 'seo_title_carousel', 'en', 'Carousel – Slider for Your Homepage', '2026-03-02 16:54:08'),
(28, 'seo_description_carousel', 'en', 'Add a modern image and text slider to your homepage.', '2026-03-02 16:54:08'),
(29, 'seo_title_carousel', 'it', 'Carousel – Slider per la tua homepage', '2026-03-02 16:54:08'),
(30, 'seo_description_carousel', 'it', 'Aggiungi uno slider moderno alla tua homepage.', '2026-03-02 16:54:08'),
(31, 'seo_title_contact', 'de', 'Kontakt – Nimm Kontakt mit dem Nexpell-Team auf', '2026-03-02 16:54:08'),
(32, 'seo_description_contact', 'de', 'Du hast Fragen oder Feedback? Nutze unser Kontaktformular – wir freuen uns auf deine Nachricht.', '2026-03-02 16:54:08'),
(33, 'seo_title_contact', 'en', 'Contact – Get in Touch with the Nexpell Team', '2026-03-02 16:54:08'),
(34, 'seo_description_contact', 'en', 'Have questions or feedback? Use our contact form to reach the Nexpell team.', '2026-03-02 16:54:08'),
(35, 'seo_title_contact', 'it', 'Contatto – Mettiti in contatto con il team Nexpell', '2026-03-02 16:54:08'),
(36, 'seo_description_contact', 'it', 'Hai domande o suggerimenti? Usa il modulo di contatto per scriverci.', '2026-03-02 16:54:08'),
(37, 'seo_title_counter', 'de', 'Besucherzähler – Statistische Auswertung', '2026-03-02 16:54:08'),
(38, 'seo_description_counter', 'de', 'Zeigt Besucherzahlen und Statistikdaten im Adminbereich aus Datenschutzgründen nur intern an.', '2026-03-02 16:54:08'),
(39, 'seo_title_counter', 'en', 'Visitor Counter – Internal Statistics', '2026-03-02 16:54:08'),
(40, 'seo_description_counter', 'en', 'Shows visitor counts and statistics internally in the admin area.', '2026-03-02 16:54:08'),
(41, 'seo_title_counter', 'it', 'Contatore visitatori – Statistiche interne', '2026-03-02 16:54:08'),
(42, 'seo_description_counter', 'it', 'Mostra conteggi dei visitatori e statistiche solo internamente.', '2026-03-02 16:54:08'),
(43, 'seo_title_default', 'de', 'Nexpell CMS – Das modulare CMS für Communities und Clans', '2026-03-02 16:54:08'),
(44, 'seo_description_default', 'de', 'Modernes Open-Source-CMS, modular, flexibel und kostenlos.', '2026-03-02 16:54:08'),
(45, 'seo_title_default', 'en', 'Nexpell CMS – The Modular CMS for Communities and Clans', '2026-03-02 16:54:08'),
(46, 'seo_description_default', 'en', 'A modern modular open-source CMS for communities and clans.', '2026-03-02 16:54:08'),
(47, 'seo_title_default', 'it', 'Nexpell CMS – Il CMS modulare per community e clan', '2026-03-02 16:54:08'),
(48, 'seo_description_default', 'it', 'Un CMS open-source moderno, modulare e completamente gratuito.', '2026-03-02 16:54:08'),
(49, 'seo_title_discord', 'de', 'Nexpell Discord – Community & Support', '2026-03-02 16:54:08'),
(50, 'seo_description_discord', 'de', 'Tritt dem offiziellen Nexpell-Discord bei und erhalte direkten Support vom Team.', '2026-03-02 16:54:08'),
(51, 'seo_title_discord', 'en', 'Nexpell Discord – Community and Support', '2026-03-02 16:54:08'),
(52, 'seo_description_discord', 'en', 'Join the official Nexpell Discord to connect with the community and get support.', '2026-03-02 16:54:08'),
(53, 'seo_title_discord', 'it', 'Nexpell Discord – Community e Supporto', '2026-03-02 16:54:08'),
(54, 'seo_description_discord', 'it', 'Unisciti al Discord ufficiale di Nexpell per parlare con la community e ricevere supporto.', '2026-03-02 16:54:08'),
(55, 'seo_title_downloads', 'de', 'Downloads – Erweiterungen für dein Nexpell CMS', '2026-03-02 16:54:08'),
(56, 'seo_description_downloads', 'de', 'Lade Module, Themes und Erweiterungen für dein Nexpell CMS herunter.', '2026-03-02 16:54:08'),
(57, 'seo_title_downloads', 'en', 'Downloads – Extensions for Your Nexpell CMS', '2026-03-02 16:54:08'),
(58, 'seo_description_downloads', 'en', 'Download modules, themes and extensions for your Nexpell CMS.', '2026-03-02 16:54:08'),
(59, 'seo_title_downloads', 'it', 'Download – Estensioni per il tuo CMS Nexpell', '2026-03-02 16:54:08'),
(60, 'seo_description_downloads', 'it', 'Scarica moduli, temi ed estensioni per il tuo CMS Nexpell.', '2026-03-02 16:54:08'),
(61, 'seo_title_entwicklungshistorie', 'de', 'Entwicklungshistorie – Versionsübersicht', '2026-03-02 16:54:08'),
(62, 'seo_description_entwicklungshistorie', 'de', 'Alle Änderungen, Versionen und Fortschritte in der Entwicklung von Nexpell.', '2026-03-02 16:54:08'),
(63, 'seo_title_entwicklungshistorie', 'en', 'Development History – Version Overview', '2026-03-02 16:54:08'),
(64, 'seo_description_entwicklungshistorie', 'en', 'See all updates and versions of Nexpell in one place.', '2026-03-02 16:54:08'),
(65, 'seo_title_entwicklungshistorie', 'it', 'Cronologia sviluppo – Panoramica versioni', '2026-03-02 16:54:08'),
(66, 'seo_description_entwicklungshistorie', 'it', 'Visualizza aggiornamenti e versioni di Nexpell.', '2026-03-02 16:54:08'),
(67, 'seo_title_forum', 'de', 'Community Forum – Fragen, Hilfe & Austausch', '2026-03-02 16:54:08'),
(68, 'seo_description_forum', 'de', 'Stelle Fragen und tausche dich mit anderen Nexpell-Nutzern im Forum aus.', '2026-03-02 16:54:08'),
(69, 'seo_title_forum', 'en', 'Community Forum – Questions, Help & Exchange', '2026-03-02 16:54:08'),
(70, 'seo_description_forum', 'en', 'Ask questions and connect with other Nexpell users in the forum.', '2026-03-02 16:54:08'),
(71, 'seo_title_forum', 'it', 'Forum della community – Domande, aiuto e confronto', '2026-03-02 16:54:08'),
(72, 'seo_description_forum', 'it', 'Fai domande e confrontati con altri utenti della community.', '2026-03-02 16:54:08'),
(73, 'seo_title_gallery', 'de', 'Galerie – Bilder und Alben anzeigen', '2026-03-02 16:54:08'),
(74, 'seo_description_gallery', 'de', 'Erstelle Bildergalerien und Alben für Events, Projekte oder Community-Beiträge.', '2026-03-02 16:54:08'),
(75, 'seo_title_gallery', 'en', 'Gallery – Display Images and Albums', '2026-03-02 16:54:08'),
(76, 'seo_description_gallery', 'en', 'Create image galleries and albums.', '2026-03-02 16:54:08'),
(77, 'seo_title_gallery', 'it', 'Galleria – Mostra immagini e album', '2026-03-02 16:54:08'),
(78, 'seo_description_gallery', 'it', 'Crea gallerie e album di immagini.', '2026-03-02 16:54:08'),
(79, 'seo_title_gametracker', 'de', 'Game Server Übersicht – Echtzeit-Serverstatus', '2026-03-02 16:54:08'),
(80, 'seo_description_gametracker', 'de', 'Überwache deine Gameserver in Echtzeit: Spieler, Karten, Status und mehr.', '2026-03-02 16:54:08'),
(81, 'seo_title_gametracker', 'en', 'Game Server Overview – Real-Time Server Info', '2026-03-02 16:54:08'),
(82, 'seo_description_gametracker', 'en', 'Monitor your game servers in real time: players, maps, versions and server status.', '2026-03-02 16:54:08'),
(83, 'seo_title_gametracker', 'it', 'Panoramica server di gioco – Stato in tempo reale', '2026-03-02 16:54:08'),
(84, 'seo_description_gametracker', 'it', 'Monitora i tuoi server di gioco in tempo reale: giocatori, mappe e stato del server.', '2026-03-02 16:54:08'),
(85, 'seo_title_imprint', 'de', 'Impressum – Rechtliche Angaben zu Nexpell', '2026-03-02 16:54:08'),
(86, 'seo_description_imprint', 'de', 'Rechtliche Informationen und Verantwortliche gemäß §5 TMG.', '2026-03-02 16:54:08'),
(87, 'seo_title_imprint', 'en', 'Legal Notice – Company and Legal Information about Nexpell', '2026-03-02 16:54:08'),
(88, 'seo_description_imprint', 'en', 'Legal information and responsible parties in accordance with §5 TMG.', '2026-03-02 16:54:08'),
(89, 'seo_title_imprint', 'it', 'Note legali – Informazioni legali su Nexpell', '2026-03-02 16:54:08'),
(90, 'seo_description_imprint', 'it', 'Informazioni legali e responsabili secondo il §5 TMG.', '2026-03-02 16:54:08'),
(91, 'seo_title_lastlogin', 'de', 'Letzter Login – Aktivität deiner Nutzer', '2026-03-02 16:54:08'),
(92, 'seo_description_lastlogin', 'de', 'Zeigt an, wann Nutzer zuletzt online waren.', '2026-03-02 16:54:08'),
(93, 'seo_title_lastlogin', 'en', 'Last Login – User Activity Overview', '2026-03-02 16:54:08'),
(94, 'seo_description_lastlogin', 'en', 'Shows last online times.', '2026-03-02 16:54:08'),
(95, 'seo_title_lastlogin', 'it', 'Ultimo accesso – Attività utenti', '2026-03-02 16:54:08'),
(96, 'seo_description_lastlogin', 'it', 'Mostra l’ultimo accesso degli utenti.', '2026-03-02 16:54:08'),
(97, 'seo_title_linklist', 'de', 'Link- & Empfehlungslisten – Nützliche Ressourcen', '2026-03-02 16:54:08'),
(98, 'seo_description_linklist', 'de', 'Sammlung hilfreicher Links.', '2026-03-02 16:54:08'),
(99, 'seo_title_linklist', 'en', 'Link & Recommendation List – Useful Resources', '2026-03-02 16:54:08'),
(100, 'seo_description_linklist', 'en', 'List of useful links.', '2026-03-02 16:54:08'),
(101, 'seo_title_linklist', 'it', 'Lista link e raccomandazioni – Risorse utili', '2026-03-02 16:54:08'),
(102, 'seo_description_linklist', 'it', 'Lista di link utili.', '2026-03-02 16:54:08'),
(103, 'seo_title_livevisitor', 'de', 'Live Besucher – Echtzeit-Statistiken', '2026-03-02 16:54:08'),
(104, 'seo_description_livevisitor', 'de', 'Überwache Live-Besucher.', '2026-03-02 16:54:08'),
(105, 'seo_title_livevisitor', 'en', 'Live Visitor – Real-Time Analytics', '2026-03-02 16:54:08'),
(106, 'seo_description_livevisitor', 'en', 'Monitor real-time visitors.', '2026-03-02 16:54:08'),
(107, 'seo_title_livevisitor', 'it', 'Visitatori live – Statistiche in tempo reale', '2026-03-02 16:54:08'),
(108, 'seo_description_livevisitor', 'it', 'Monitora visitatori live.', '2026-03-02 16:54:08'),
(109, 'seo_title_masterlist', 'de', 'Call of Duty Masterlist – Serverübersicht', '2026-03-02 16:54:08'),
(110, 'seo_description_masterlist', 'de', 'Zeigt verfügbare CoD-Server.', '2026-03-02 16:54:08'),
(111, 'seo_title_masterlist', 'en', 'Call of Duty Masterlist – Server Overview', '2026-03-02 16:54:08'),
(112, 'seo_description_masterlist', 'en', 'Displays available CoD servers.', '2026-03-02 16:54:08'),
(113, 'seo_title_masterlist', 'it', 'Masterlist Call of Duty – Panoramica server', '2026-03-02 16:54:08'),
(114, 'seo_description_masterlist', 'it', 'Mostra server CoD.', '2026-03-02 16:54:08'),
(115, 'seo_title_messenger', 'de', 'Messenger – Private Nachrichten', '2026-03-02 16:54:08'),
(116, 'seo_description_messenger', 'de', 'Private Nachrichten senden.', '2026-03-02 16:54:08'),
(117, 'seo_title_messenger', 'en', 'Messenger – Private Messages', '2026-03-02 16:54:08'),
(118, 'seo_description_messenger', 'en', 'Send private messages.', '2026-03-02 16:54:08'),
(119, 'seo_title_messenger', 'it', 'Messenger – Messaggi privati', '2026-03-02 16:54:08'),
(120, 'seo_description_messenger', 'it', 'Invia messaggi privati.', '2026-03-02 16:54:08'),
(121, 'seo_title_partners', 'de', 'Partner – Unterstützer und Kooperationen', '2026-03-02 16:54:08'),
(122, 'seo_description_partners', 'de', 'Stelle Partner übersichtlich dar.', '2026-03-02 16:54:08'),
(123, 'seo_title_partners', 'en', 'Partners – Supporters and Cooperations', '2026-03-02 16:54:08'),
(124, 'seo_description_partners', 'en', 'Show partners clearly.', '2026-03-02 16:54:08'),
(125, 'seo_title_partners', 'it', 'Partner – Sponsor e collaborazioni', '2026-03-02 16:54:08'),
(126, 'seo_description_partners', 'it', 'Mostra gli sponsor chiaramente.', '2026-03-02 16:54:08'),
(127, 'seo_title_pricing', 'de', 'Preistabellen – Kosten & Leistungen', '2026-03-02 16:54:08'),
(128, 'seo_description_pricing', 'de', 'Erstelle Preislisten.', '2026-03-02 16:54:08'),
(129, 'seo_title_pricing', 'en', 'Pricing – Plans and Features Overview', '2026-03-02 16:54:08'),
(130, 'seo_description_pricing', 'en', 'Create pricing tables.', '2026-03-02 16:54:08'),
(131, 'seo_title_pricing', 'it', 'Prezzi – Panoramica piani e funzionalità', '2026-03-02 16:54:08'),
(132, 'seo_description_pricing', 'it', 'Crea listini prezzi.', '2026-03-02 16:54:08'),
(133, 'seo_title_privacy_policy', 'de', 'Datenschutz – Umgang mit deinen Daten', '2026-03-02 16:54:08'),
(134, 'seo_description_privacy_policy', 'de', 'Erfahre, wie wir deine Daten schützen. DSGVO-konform und transparent.', '2026-03-02 16:54:08'),
(135, 'seo_title_privacy_policy', 'en', 'Privacy Policy – How We Handle Your Data', '2026-03-02 16:54:08'),
(136, 'seo_description_privacy_policy', 'en', 'Learn how we protect your data. GDPR-compliant and transparent.', '2026-03-02 16:54:08'),
(137, 'seo_title_privacy_policy', 'it', 'Privacy – Come trattiamo i tuoi dati', '2026-03-02 16:54:08'),
(138, 'seo_description_privacy_policy', 'it', 'Scopri come proteggiamo i tuoi dati in conformità al GDPR.', '2026-03-02 16:54:08'),
(139, 'seo_title_rules', 'de', 'Regeln – Community- & Serverregeln', '2026-03-02 16:54:08'),
(140, 'seo_description_rules', 'de', 'Verwalte Regeln klar strukturiert.', '2026-03-02 16:54:08'),
(141, 'seo_title_rules', 'en', 'Rules – Community & Server Guidelines', '2026-03-02 16:54:08'),
(142, 'seo_description_rules', 'en', 'Manage rules clearly.', '2026-03-02 16:54:08'),
(143, 'seo_title_rules', 'it', 'Regole – Linee guida', '2026-03-02 16:54:08'),
(144, 'seo_description_rules', 'it', 'Gestisci regole chiaramente.', '2026-03-02 16:54:08'),
(145, 'seo_title_search', 'de', 'Suche – Inhalte schnell finden', '2026-03-02 16:54:08'),
(146, 'seo_description_search', 'de', 'Durchsuche Seiten schnell und effizient.', '2026-03-02 16:54:08'),
(147, 'seo_title_search', 'en', 'Search – Find Content Quickly', '2026-03-02 16:54:08'),
(148, 'seo_description_search', 'en', 'Search content efficiently.', '2026-03-02 16:54:08'),
(149, 'seo_title_search', 'it', 'Ricerca – Trova contenuti facilmente', '2026-03-02 16:54:08'),
(150, 'seo_description_search', 'it', 'Cerca contenuti facilmente.', '2026-03-02 16:54:08'),
(151, 'seo_title_seo', 'de', 'SEO Manager – Suchmaschinenoptimierung', '2026-03-02 16:54:08'),
(152, 'seo_description_seo', 'de', 'Verwalte Meta-Daten deiner Website.', '2026-03-02 16:54:08'),
(153, 'seo_title_seo', 'en', 'SEO Manager – Search Engine Optimization', '2026-03-02 16:54:08'),
(154, 'seo_description_seo', 'en', 'Manage meta-data.', '2026-03-02 16:54:08'),
(155, 'seo_title_seo', 'it', 'SEO Manager – Ottimizzazione motori di ricerca', '2026-03-02 16:54:08'),
(156, 'seo_description_seo', 'it', 'Gestisci meta-dati.', '2026-03-02 16:54:08'),
(157, 'seo_title_shoutbox', 'de', 'Shoutbox – Kurznachrichten deiner Community', '2026-03-02 16:54:08'),
(158, 'seo_description_shoutbox', 'de', 'Poste schnelle Nachrichten und bleibe mit deiner Community verbunden.', '2026-03-02 16:54:08'),
(159, 'seo_title_shoutbox', 'en', 'Shoutbox – Quick Messages for Your Community', '2026-03-02 16:54:08'),
(160, 'seo_description_shoutbox', 'en', 'Post short messages and stay connected with your community.', '2026-03-02 16:54:08'),
(161, 'seo_title_shoutbox', 'it', 'Shoutbox – Messaggi rapidi per la tua community', '2026-03-02 16:54:08'),
(162, 'seo_description_shoutbox', 'it', 'Invia messaggi brevi e rimani in contatto con la tua community.', '2026-03-02 16:54:08'),
(163, 'seo_title_sponsors', 'de', 'Sponsoren – Unterstützer deiner Community', '2026-03-02 16:54:08'),
(164, 'seo_description_sponsors', 'de', 'Zeige Sponsoren übersichtlich.', '2026-03-02 16:54:08'),
(165, 'seo_title_sponsors', 'en', 'Sponsors – Supporters of Your Community', '2026-03-02 16:54:08'),
(166, 'seo_description_sponsors', 'en', 'Show sponsors clearly.', '2026-03-02 16:54:08'),
(167, 'seo_title_sponsors', 'it', 'Sponsor – Sostenitori della community', '2026-03-02 16:54:08'),
(168, 'seo_description_sponsors', 'it', 'Mostra sponsor chiaramente.', '2026-03-02 16:54:08'),
(169, 'seo_title_todo', 'de', 'TODO – Offene Aufgaben und wichtige To-Dos', '2026-03-02 16:54:08'),
(170, 'seo_description_todo', 'de', 'Behalte einen Überblick über offene Aufgaben und Projektfortschritte.', '2026-03-02 16:54:08'),
(171, 'seo_title_todo', 'en', 'TODO – Open Tasks and Important To-Dos', '2026-03-02 16:54:08'),
(172, 'seo_description_todo', 'en', 'Keep track of open tasks and ongoing project steps.', '2026-03-02 16:54:08'),
(173, 'seo_title_todo', 'it', 'TODO – Compiti aperti e cose da fare importanti', '2026-03-02 16:54:08'),
(174, 'seo_description_todo', 'it', 'Tieni traccia dei compiti aperti e dei passaggi pianificati.', '2026-03-02 16:54:08'),
(175, 'seo_title_twitch', 'de', 'Twitch – Livestream auf deiner Website', '2026-03-02 16:54:08'),
(176, 'seo_description_twitch', 'de', 'Binde Twitch ein.', '2026-03-02 16:54:08'),
(177, 'seo_title_twitch', 'en', 'Twitch – Livestream on Your Website', '2026-03-02 16:54:08'),
(178, 'seo_description_twitch', 'en', 'Embed Twitch stream.', '2026-03-02 16:54:08'),
(179, 'seo_title_twitch', 'it', 'Twitch – Livestream sul tuo sito', '2026-03-02 16:54:08'),
(180, 'seo_description_twitch', 'it', 'Incorpora stream Twitch.', '2026-03-02 16:54:08'),
(181, 'seo_title_userlist', 'de', 'Mitgliederliste – Alle registrierten Nutzer im Überblick', '2026-03-02 16:54:08'),
(182, 'seo_description_userlist', 'de', 'Hier findest du alle Mitglieder der Nexpell-Community mit Profilinformationen.', '2026-03-02 16:54:08'),
(183, 'seo_title_userlist', 'en', 'Member List – All Registered Users at a Glance', '2026-03-02 16:54:08'),
(184, 'seo_description_userlist', 'en', 'See all registered members of the Nexpell community with profile info.', '2026-03-02 16:54:08'),
(185, 'seo_title_userlist', 'it', 'Lista membri – Tutti gli utenti registrati', '2026-03-02 16:54:08'),
(186, 'seo_description_userlist', 'it', 'Visualizza tutti i membri registrati della community Nexpell.', '2026-03-02 16:54:08'),
(187, 'seo_title_whoisonline', 'de', 'Who is Online – Live-Aktivität anzeigen', '2026-03-02 16:54:08'),
(188, 'seo_description_whoisonline', 'de', 'Zeigt aktive Benutzer.', '2026-03-02 16:54:08'),
(189, 'seo_title_whoisonline', 'en', 'Who is Online – Live User Activity', '2026-03-02 16:54:08'),
(190, 'seo_description_whoisonline', 'en', 'Shows live users.', '2026-03-02 16:54:08'),
(191, 'seo_title_whoisonline', 'it', 'Chi è online – Attività utenti', '2026-03-02 16:54:08'),
(192, 'seo_description_whoisonline', 'it', 'Mostra utenti attivi.', '2026-03-02 16:54:08');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `settings_site_lock`
--

CREATE TABLE `settings_site_lock` (
  `id` int(11) NOT NULL,
  `reason` text NOT NULL,
  `time` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `settings_social_media`
--

CREATE TABLE `settings_social_media` (
  `socialID` int(11) NOT NULL,
  `twitch` varchar(255) NOT NULL,
  `facebook` varchar(255) NOT NULL,
  `twitter` varchar(255) NOT NULL,
  `youtube` varchar(255) NOT NULL,
  `rss` varchar(255) NOT NULL,
  `vine` varchar(255) NOT NULL,
  `flickr` varchar(255) NOT NULL,
  `linkedin` varchar(255) NOT NULL,
  `instagram` varchar(255) NOT NULL,
  `since` varchar(255) NOT NULL,
  `gametracker` varchar(255) NOT NULL,
  `discord` varchar(255) NOT NULL,
  `steam` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `settings_social_media`
--

INSERT INTO `settings_social_media` (`socialID`, `twitch`, `facebook`, `twitter`, `youtube`, `rss`, `vine`, `flickr`, `linkedin`, `instagram`, `since`, `gametracker`, `discord`, `steam`) VALUES
(1, 'https://www.twitch.tv/pulsradiocom', 'https://www.facebook.com/nexpell', 'https://twitter.com/nexpell', '', '', '', '', '', '', '2025', '85.14.228.228:28960', 'https://www.discord.gg/kErxPxb', '');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `settings_static`
--

CREATE TABLE `settings_static` (
  `staticID` int(11) NOT NULL,
  `categoryID` int(11) NOT NULL DEFAULT 0,
  `date` int(14) NOT NULL,
  `access_roles` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `settings_themes`
--

CREATE TABLE `settings_themes` (
  `themeID` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `modulname` varchar(100) NOT NULL,
  `slug` varchar(120) NOT NULL DEFAULT 'default',
  `pfad` varchar(255) NOT NULL,
  `manifest_path` varchar(255) DEFAULT NULL,
  `layout_file` varchar(255) DEFAULT NULL,
  `preview_image` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `version` varchar(11) NOT NULL,
  `active` int(11) DEFAULT NULL,
  `themename` varchar(255) NOT NULL,
  `navbar_class` varchar(50) NOT NULL,
  `navbar_theme` varchar(10) NOT NULL,
  `express_active` int(11) NOT NULL DEFAULT 0,
  `logo_pic` varchar(255) DEFAULT '0',
  `reg_pic` varchar(255) NOT NULL,
  `headlines` varchar(255) DEFAULT '0',
  `sort` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `settings_themes`
--

INSERT INTO `settings_themes` (`themeID`, `name`, `modulname`, `slug`, `pfad`, `manifest_path`, `layout_file`, `preview_image`, `description`, `version`, `active`, `themename`, `navbar_class`, `navbar_theme`, `express_active`, `logo_pic`, `reg_pic`, `headlines`, `sort`) VALUES
(1, 'Default', 'default', 'default', 'default', 'includes/themes/default/theme.json', 'index.php', 'images/default_logo.png', 'Standard-Nexpell-Theme mit Bootstrap-Basis und klassischer CMS-Integration.', '0.3', 0, 'cyborg', 'bg-dark', 'dark', 0, 'default_logo.png', 'default_login_bg.jpg', 'headlines_03.css', 1),
(2, 'test', 'test', 'test', 'test', 'includes/themes/test/theme.json', 'index.php', 'assets/img/profile/profile-bg-5.webp', 'Importiertes Craftivo-Theme mit bereinigter Asset-Reihenfolge.', '1.0.0', 0, 'yeti', 'shadow-sm', 'auto', 0, 'default_logo.png', 'default_login_bg.jpg', 'headlines_03.css', 0),
(3, 'Craftivo', 'craftivo', 'craftivo', 'craftivo', 'includes/themes/craftivo/theme.json', 'index.php', 'assets/img/profile/profile-bg-5.webp', '', '1.0.0', 0, 'yeti', 'bg-dark', 'dark', 0, 'default_logo.png', 'default_login_bg.jpg', 'headlines_03.css', 0),
(4, 'EasyFolio', 'easyfolio', 'easyfolio', 'easyfolio', 'includes/themes/easyfolio/theme.json', 'index.php', 'assets/img/profile/profile-1.webp', 'EasyFolio-Template mit bereinigter Asset-Reihenfolge und eigenem Import-Layout.', '1.0.0', 0, 'yeti', 'bg-dark', 'dark', 0, 'default_logo.png', 'default_login_bg.jpg', 'headlines_03.css', 0),
(5, 'Instant', 'instant', 'instant', 'instant', 'includes/themes/instant/theme.json', 'index.php', 'assets/img/about/about-18.webp', '', '1.0.0', 0, 'yeti', 'bg-dark', 'auto', 0, 'default_logo.png', 'default_login_bg.jpg', 'headlines_03.css', 0),
(6, 'LeadPage', 'leadpage', 'leadpage', 'leadpage', 'includes/themes/leadpage/theme.json', 'index.php', '', '', '1.0.0', 0, 'yeti', 'bg-dark', 'dark', 0, 'default_logo.png', 'default_login_bg.jpg', 'headlines_03.css', 0),
(7, 'EasyTest', 'easytest', 'easytest', 'easytest', 'includes/themes/easytest/theme.json', 'index.php', '', 'mein eigenes', '1.0.0', 1, 'yeti', 'bg-primary', 'light', 0, 'default_logo.png', 'default_login_bg.jpg', 'headlines_03.css', 0);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `settings_themes_installed`
--

CREATE TABLE `settings_themes_installed` (
  `themeID` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `modulname` varchar(255) NOT NULL,
  `version` varchar(20) NOT NULL,
  `author` varchar(100) DEFAULT NULL,
  `url` varchar(255) NOT NULL,
  `folder` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `installed_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `settings_themes_installed`
--

INSERT INTO `settings_themes_installed` (`themeID`, `name`, `modulname`, `version`, `author`, `url`, `folder`, `description`, `installed_date`) VALUES
(1, 'Lux', 'lux', '5.3.3', 'Bootswatch', 'https://bootswatch.com/lux/', 'lux', '[[lang:de]]Ein luxuriöses Theme mit klaren Linien.[[lang:en]]A luxurious theme with clean lines.[[lang:it]]Un tema lussuoso con linee pulite.', '2025-06-19 12:20:03'),
(2, 'Yeti', 'yeti', '5.3.3', 'Bootswatch', 'https://bootswatch.com/yeti/', 'yeti', '[[lang:de]]Ein schlichtes, freundliches Theme mit zurückhaltender Farbgebung, das Übersichtlichkeit und Nutzerfreundlichkeit fördert. Besonders geeignet für professionelle und einfache Webseiten.[[lang:en]]A simple, friendly theme with subdued colors, promoting clarity and user-friendliness. Particularly suitable for professional and straightforward websites.[[lang:it]]Un tema semplice e accogliente con colori sobri, che favorisce chiarezza e facilità d&#039;uso. Particolarmente adatto per siti web professionali e lineari.', '2026-03-02 16:57:53'),
(3, 'Cyborg', 'cyborg', '5.3.3', 'Bootswatch', 'https://bootswatch.com/cyborg/', 'cyborg', '[[lang:de]]Ein dunkles, futuristisches Theme mit starken Kontrasten und modernen Akzenten. Optimal geeignet für technikaffine Webseiten und Projekte, die einen High-Tech-Look bevorzugen.[[lang:en]]A dark, futuristic theme with strong contrasts and modern accents. Perfectly suited for tech-savvy websites and projects that prefer a high-tech look.[[lang:it]]Un tema scuro e futuristico con forti contrasti e accenti moderni. Perfetto per siti web tecnologici e progetti che prediligono un aspetto high-tech.', '2026-03-07 10:43:35'),
(4, 'Morph', 'morph', '5.3.3', 'Bootswatch', 'https://bootswatch.com/morph/', 'morph', '[[lang:de]]Ein auffälliges, rundes Theme mit markanten Formen, das moderne und dynamische Designs unterstützt. Ideal für Projekte mit hohem Designanspruch und visuellem Fokus.[[lang:en]]A striking, rounded theme with prominent shapes that supports modern and dynamic designs. Ideal for projects with high design standards and visual focus.[[lang:it]]Un tema audace e arrotondato con forme marcate, che supporta design moderni e dinamici. Ideale per progetti con elevati standard di design e focus visivo.', '2026-03-07 12:01:03'),
(5, 'Slate', 'slate', '5.3.3', 'Bootswatch', 'https://bootswatch.com/slate/', 'slate', '[[lang:de]]Ein dunkles, seriöses Theme mit klaren Linien, das Professionalität und Fokus vermittelt. Es eignet sich für Business-Webseiten und Projekte mit ernstem Charakter.[[lang:en]]A dark, serious theme with clean lines that conveys professionalism and focus. Suitable for business websites and projects with a serious character.[[lang:it]]Un tema scuro e serio con linee pulite, che trasmette professionalità e concentrazione. Adatto a siti web aziendali e progetti di carattere serio.', '2026-03-12 21:36:20'),
(6, 'Brite', 'brite', '5.3.3', 'Bootswatch', 'https://bootswatch.com/brite/', 'brite', '[[lang:de]]Ein helles, freundliches Theme mit klaren Farben, das sich hervorragend für moderne Websites eignet und durch seine angenehme Farbharmonie besticht. Ideal für Projekte, die eine einladende und übersichtliche Gestaltung benötigen.[[lang:en]]A bright, friendly theme with clear colors, perfect for modern websites, distinguished by its pleasant color harmony. Ideal for projects that require an inviting and well-organized design.[[lang:it]]Un tema luminoso e accogliente con colori chiari, perfetto per siti web moderni, caratterizzato da una piacevole armonia cromatica. Ideale per progetti che richiedono un design invitante e ben organizzato.', '2026-03-14 12:42:22'),
(12, 'EasyTest', 'easytest', '1.0.0', 't-seven', 'https://www.nexpell.de', 'easytest', 'mein eigenes', '2026-03-20 20:20:30'),
(18, 'Craftivo', 'craftivo', '', '', '', 'craftivo', '', '2026-03-22 13:49:44');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `settings_theme_options`
--

CREATE TABLE `settings_theme_options` (
  `optionID` int(10) UNSIGNED NOT NULL,
  `theme_slug` varchar(120) NOT NULL,
  `option_key` varchar(120) NOT NULL,
  `option_value` longtext DEFAULT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `settings_theme_options`
--

INSERT INTO `settings_theme_options` (`optionID`, `theme_slug`, `option_key`, `option_value`, `updated_at`) VALUES
(1, 'easytest', 'builder_runtime', '{\"hero_title\":\"EasyTest\",\"hero_text\":\"Eigenes Nexpell Theme als Startpunkt.\",\"cta_label\":\"Mehr erfahren\",\"layout_preset\":\"right-sidebar\",\"page_width\":\"boxed\",\"content_width\":\"normal\",\"column_ratio\":\"75-25\",\"two_sidebar_ratio\":\"3-6-3\",\"section_spacing\":\"normal\",\"card_style\":\"outlined\",\"card_radius\":\"0\",\"card_background\":\"#FFFFFF\",\"card_border_width\":\"1\",\"card_border_color\":\"#dee2e6\",\"button_radius\":\"0\",\"input_radius\":\"0\",\"heading_weight\":\"300\",\"hero_radius\":\"0\",\"hero_style\":\"standard\",\"nav_style\":\"solid\",\"nav_variant\":\"primary\",\"nav_link_weight\":\"500\",\"nav_font_size\":\"1rem\",\"nav_text_transform\":\"none\",\"nav_border_width\":\"1\",\"pagination_radius\":\"3px\",\"pagination_border_width\":\"0\",\"pagination_gap\":\".25rem\",\"pagination_font_weight\":\"300\",\"pagination_color\":\"#008CBA\",\"pagination_background\":\"#FFFFFF\",\"pagination_border_color\":\"#FFFFFF\",\"pagination_hover_color\":\"#FFFFFF\",\"pagination_hover_background\":\"#007095\",\"pagination_hover_border_color\":\"#007095\",\"pagination_active_color\":\"#FFFFFF\",\"pagination_active_background\":\"#008CBA\",\"pagination_active_border_color\":\"#008CBA\",\"color_preset_key\":\"\",\"nav_width\":\"content\",\"nav_radius\":\"18\",\"nav_top_spacing\":\"12\",\"nav_background\":\"#008CBA\",\"nav_link\":\"#111827\",\"nav_hover\":\"#111827\",\"nav_active\":\"#111827\",\"nav_dropdown_background\":\"#FFFFFF\",\"show_hero\":false,\"colors\":{\"accent\":\"#008CBA\",\"page_top\":\"#fcfcfc\",\"page_bg\":\"#ffffff\",\"surface\":\"#FFFFFF\",\"text\":\"#222222\"}}', '2026-03-25 00:42:36'),
(343, 'craftivo', 'builder_runtime', '{\"hero_title\":\"Craftivo\",\"hero_text\":\"Eigenes Nexpell Theme als Startpunkt.\",\"cta_label\":\"Mehr erfahren\",\"layout_preset\":\"right-sidebar\",\"page_width\":\"wide\",\"content_width\":\"normal\",\"column_ratio\":\"75-25\",\"two_sidebar_ratio\":\"3-6-3\",\"section_spacing\":\"normal\",\"card_style\":\"elevated\",\"card_background\":\"#252525\",\"card_border_width\":\"1\",\"card_border_color\":\"#1a2230\",\"hero_style\":\"standard\",\"nav_style\":\"glass\",\"nav_width\":\"full\",\"nav_radius\":\"0\",\"nav_top_spacing\":\"0\",\"nav_background\":\"#ffffff\",\"nav_link\":\"#212529\",\"nav_hover\":\"#1f6feb\",\"nav_active\":\"#1f6feb\",\"nav_dropdown_background\":\"#ffffff\",\"show_hero\":true,\"colors\":{\"accent\":\"#ff4d4f\",\"page_top\":\"#1a2230\",\"page_bg\":\"#11151b\",\"surface\":\"#252525\",\"text\":\"#ffffff\"}}', '2026-03-22 13:55:29');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `settings_widgets`
--

CREATE TABLE `settings_widgets` (
  `widget_key` varchar(128) NOT NULL,
  `title` varchar(255) NOT NULL DEFAULT '',
  `modulname` varchar(100) NOT NULL DEFAULT '',
  `plugin` varchar(64) NOT NULL DEFAULT '',
  `description` text DEFAULT NULL,
  `allowed_zones` varchar(255) NOT NULL DEFAULT '',
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `version` varchar(16) NOT NULL DEFAULT '1.0.0',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `settings_widgets`
--

INSERT INTO `settings_widgets` (`widget_key`, `title`, `modulname`, `plugin`, `description`, `allowed_zones`, `active`, `version`, `created_at`) VALUES
('widget_agency_header', 'Agency Header', 'carousel', 'carousel', NULL, 'undertop', 1, '1.0.0', '2026-03-04 20:34:08'),
('widget_articles_content', 'Artikel Widget Content', 'articles', 'articles', NULL, 'maintop,mainbottom', 1, '1.0.0', '2026-03-07 12:56:35'),
('widget_articles_news', 'Artikel Widget News', 'articles', 'articles', NULL, 'maintop,mainbottom', 1, '1.0.0', '2026-03-07 12:56:35'),
('widget_articles_sidebar', 'Artikel Widget Sidebar', 'articles', 'articles', NULL, 'left,right', 1, '1.0.0', '2026-03-07 12:56:35'),
('widget_carousel_header', 'Carousel Header', 'carousel', 'carousel', NULL, 'top,undertop', 1, '1.0.0', '2026-03-04 20:34:08'),
('widget_discord_sidebar', 'Discord Widget Sidebar', 'discord', 'discord', NULL, 'left,right', 1, '1.0.0', '2026-03-05 19:58:55'),
('widget_forum_content', 'Forum Content Widget', 'forum', 'forum', NULL, 'maintop', 1, '1.0.0', '2026-03-11 19:27:22'),
('widget_forum_sidebar', 'Forum Sidebar Widget', 'forum', 'forum', NULL, 'left,right', 1, '1.0.0', '2026-03-11 19:27:22'),
('widget_gametracker_sidebar', 'Gametracker Sidebar', 'gametracker', 'gametracker', NULL, 'left,right', 1, '1.0.3.3', '2026-03-07 15:57:39'),
('widget_lastregistered_sidebar', 'Last Registered Sidebar', 'userlist', 'userlist', NULL, 'left,right', 1, '1.0.0', '2026-03-04 21:03:38'),
('widget_memberslist_content', 'User Memberlist', 'userlist', 'userlist', NULL, 'maintop,mainbottom', 1, '1.0.0', '2026-03-04 21:03:38'),
('widget_news_carousel', 'News Carousel', 'news', 'news', NULL, '', 1, '1.0.0', '2026-03-08 13:40:12'),
('widget_news_featured_list', 'News Featured List', 'news', 'news', NULL, '', 1, '1.0.0', '2026-03-08 13:40:12'),
('widget_news_flip', 'News Flip', 'news', 'news', NULL, '', 1, '1.0.0', '2026-03-08 13:40:12'),
('widget_news_magazine', 'News Magazine', 'news', 'news', NULL, '', 1, '1.0.0', '2026-03-08 13:40:12'),
('widget_news_masonry', 'News Masonry', 'news', 'news', NULL, '', 1, '1.0.0', '2026-03-08 13:40:12'),
('widget_news_topnews', 'News Topnews', 'news', 'news', NULL, '', 1, '1.0.0', '2026-03-08 13:40:12'),
('widget_parallax_header', 'Parallax Header', 'carousel', 'carousel', NULL, 'undertop', 1, '1.0.0', '2026-03-04 20:34:08'),
('widget_raidplaner_content', 'Raidplaner Widget Content', 'raidplaner', 'raidplaner', NULL, '', 1, '1.0.0', '2026-03-11 19:25:08'),
('widget_raidplaner_sidebar', 'Raidplaner Widget Sidebar', 'raidplaner', 'raidplaner', NULL, '', 1, '1.0.0', '2026-03-11 19:25:08'),
('widget_search_sidebar', 'Search Sidebar', 'search', 'search', NULL, '', 1, '1.0.3.3', '2026-03-11 20:38:19'),
('widget_shoutbox_sidebar', 'Shoutbox Sidebar', 'shoutbox', 'shoutbox', NULL, 'left,right', 1, '1.0.0', '2026-03-08 15:55:53'),
('widget_sticky_header', 'Sticky Header', 'carousel', 'carousel', NULL, 'top,undertop', 1, '1.0.0', '2026-03-04 20:34:08'),
('widget_teamspeak', 'Teamspeak Sidebar Widget', 'teamspeak', 'teamspeak', NULL, 'left,right', 1, '1.0.0', '2026-03-06 23:00:48'),
('widget_teamspeak_small', 'Teamspeak Sidebar Small Widget', 'teamspeak', 'teamspeak', NULL, 'left,right', 1, '1.0.0', '2026-03-06 23:00:48'),
('widget_useronline_sidebar', 'User Online Sidebar', 'userlist', 'userlist', NULL, 'left,right', 1, '1.0.0', '2026-03-04 21:03:38');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `settings_widgets_positions`
--

CREATE TABLE `settings_widgets_positions` (
  `id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `modulname` varchar(100) NOT NULL DEFAULT '',
  `widget_key` varchar(128) NOT NULL DEFAULT '',
  `position` varchar(32) NOT NULL DEFAULT 'top',
  `page` varchar(64) NOT NULL DEFAULT 'index',
  `instance_id` varchar(64) NOT NULL DEFAULT '',
  `settings` text DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `settings_widgets_positions`
--

INSERT INTO `settings_widgets_positions` (`id`, `title`, `modulname`, `widget_key`, `position`, `page`, `instance_id`, `settings`, `sort_order`) VALUES
(806, 'Artikel Widget Sidebar', 'articles', 'widget_articles_sidebar', 'left', 'about', 'w_lyu98uh4', '{}', 0),
(807, 'User Online Sidebar', 'userlist', 'widget_useronline_sidebar', 'left', 'about', 'w_7bx98z6t', '{}', 1),
(808, 'Artikel Widget Sidebar', 'articles', 'widget_articles_sidebar', 'left', 'index', 'w_tgymbkeb', '{}', 0),
(809, 'User Online Sidebar', 'userlist', 'widget_useronline_sidebar', 'left', 'index', 'w_yjnr8zfp', '{}', 1),
(810, 'Last Registered Sidebar', 'userlist', 'widget_lastregistered_sidebar', 'left', 'index', 'w_0tcv2yf6', '{}', 2),
(811, 'Shoutbox Sidebar', 'shoutbox', 'widget_shoutbox_sidebar', 'left', 'index', 'w_0mou2pxm', '{}', 3),
(812, 'Artikel Widget News', 'articles', 'widget_articles_news', 'maintop', 'index', 'w_tqmo6it3', '{}', 0),
(813, 'Forum Content Widget', 'forum', 'widget_forum_content', 'maintop', 'index', 'w_fcvueify', '{}', 1),
(814, 'User Memberlist', 'userlist', 'widget_memberslist_content', 'mainbottom', 'index', 'w_wrqo802e', '{}', 0),
(815, 'Discord Widget Sidebar', 'discord', 'widget_discord_sidebar', 'right', 'index', 'w_zw7jmzqy', '{}', 0);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `system_update_history`
--

CREATE TABLE `system_update_history` (
  `id` int(11) NOT NULL,
  `version` varchar(20) NOT NULL,
  `channel` enum('stable','beta','dev') NOT NULL,
  `build` int(11) NOT NULL DEFAULT 1,
  `installed_at` int(11) NOT NULL,
  `installed_by` int(11) DEFAULT NULL,
  `success` tinyint(1) NOT NULL DEFAULT 1,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `system_update_history`
--

INSERT INTO `system_update_history` (`id`, `version`, `channel`, `build`, `installed_at`, `installed_by`, `success`, `notes`) VALUES
(1, '1.0.2', 'stable', 1, 1772466423, 1, 1, 'Erstinstallation von Nexpell'),
(2, '1.0.2.1', 'stable', 1, 1772466500, 1, 1, 'Vorbereitung für den neuen Nexpell-Core-Updaters'),
(3, '1.0.2.2', 'stable', 1, 1772466500, 1, 1, 'Einführung des neuen Nexpell-Core-Updaters'),
(4, '1.0.2.2', 'stable', 2, 1772466515, 1, 1, 'EinfÃ¼hrung des neuen Nexpell-Core-Updaters'),
(6, '1.0.3', 'stable', 1, 1772466520, 1, 1, 'StabilitÃ¤ts- und Strukturupdate mit Verbesserungen an Navigation, SEO, Forum-Rechten und Plugin-Verwaltung.'),
(7, '1.0.3.1', 'stable', 1, 1772466520, 1, 1, 'Core Update fÃ¼r das neues ACL-Forum, verbesserter Plugin-Installer &amp; mehr StabilitÃ¤t'),
(10, '1.0.3.2', 'beta', 4, 1772466562, 1, 1, 'Weitere Anpassung an den Nexpell-Core-Updater'),
(16, '1.0.3.3', 'beta', 6, 1772553901, 1, 1, 'Core Update für das neues ACL-Forum, verbesserter Plugin-Installer mehr Stabilität'),
(18, '1.0.3.3', 'beta', 9, 1773580296, 1, 1, 'Core Update für das neues ACL-Forum, verbesserter Plugin-Installer mehr Stabilität');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tags`
--

CREATE TABLE `tags` (
  `rel` varchar(255) NOT NULL,
  `ID` int(11) NOT NULL,
  `tag` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `users`
--

CREATE TABLE `users` (
  `userID` int(11) NOT NULL,
  `registerdate` timestamp NOT NULL DEFAULT current_timestamp(),
  `lastlogin` timestamp NOT NULL DEFAULT current_timestamp(),
  `password_hash` varchar(255) NOT NULL,
  `password_pepper` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_hide` tinyint(1) NOT NULL DEFAULT 1,
  `email_change` varchar(255) NOT NULL,
  `email_activate` varchar(255) NOT NULL,
  `role` int(11) DEFAULT 1,
  `is_active` tinyint(1) DEFAULT 0,
  `is_locked` tinyint(1) NOT NULL DEFAULT 0,
  `activation_code` varchar(64) DEFAULT NULL,
  `activation_expires` int(11) DEFAULT NULL,
  `visits` int(11) NOT NULL DEFAULT 0,
  `language` varchar(2) NOT NULL,
  `last_update` datetime DEFAULT NULL,
  `login_time` datetime DEFAULT NULL,
  `last_activity` datetime DEFAULT NULL,
  `total_online_seconds` int(11) DEFAULT 0,
  `is_online` tinyint(1) NOT NULL DEFAULT 0,
  `twofa_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `twofa_method` enum('email','totp') DEFAULT 'email',
  `twofa_email_code_hash` varchar(255) DEFAULT NULL,
  `twofa_email_code_expires_at` datetime DEFAULT NULL,
  `twofa_email_last_sent_at` datetime DEFAULT NULL,
  `twofa_failed_attempts` int(11) NOT NULL DEFAULT 0,
  `twofa_locked_until` datetime DEFAULT NULL,
  `remember_device_salt` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `users`
--

INSERT INTO `users` (`userID`, `registerdate`, `lastlogin`, `password_hash`, `password_pepper`, `username`, `email`, `email_hide`, `email_change`, `email_activate`, `role`, `is_active`, `is_locked`, `activation_code`, `activation_expires`, `visits`, `language`, `last_update`, `login_time`, `last_activity`, `total_online_seconds`, `is_online`, `twofa_enabled`, `twofa_method`, `twofa_email_code_hash`, `twofa_email_code_expires_at`, `twofa_email_last_sent_at`, `twofa_failed_attempts`, `twofa_locked_until`, `remember_device_salt`) VALUES
(1, '2026-03-02 15:47:03', '2026-03-25 09:49:55', '$2y$12$F6eQoyEmyANOl.Z3CmH1Hug6grf6y0iu0qXUL.ZQ9qJwz8xYMAsOq', 'DmBUCfWaHwejpJDblP8Q00hNSkN1K3VuMzJlczFVd3gxL2NOVmNnVDgxV3V3K1V2ZEFtc1E0Q01QeVA2aWZOL3hHSHdBY2N1WnpuNlpjSW4=', 'T-Seven', 'info@nexpell.de', 1, '', '', 1, 1, 0, NULL, NULL, 0, 'de', NULL, NULL, '2026-03-25 15:44:06', 352193, 1, 0, 'email', NULL, NULL, NULL, 0, NULL, NULL),
(2, '2026-03-08 13:37:14', '2026-03-16 18:29:42', '$2y$12$GE4bbNnP8Spje7EwMPUaVubb1ByZbCGuJmnD/viJ6A2gE9COSZxom', 'VuIPZ8fyzkiIe07Otkq1yWdiOUpDaW51SEw3SksyNTY4MUJ3QXA3WWZQd3U5Snk4V1RncHU0aWFTOVZsUnkxRisxWFYwZldqQWg5M0NRSlM=', 'Lucas', 't-seven@webspell-rm.de', 1, '', '', 1, 1, 0, NULL, NULL, 0, '', NULL, NULL, NULL, 21829, 0, 0, 'email', NULL, NULL, NULL, 0, NULL, NULL),
(3, '2026-03-16 18:52:40', '2026-03-16 18:54:26', '$2y$12$7el64AcyUJciwj0e0Wb9bO5mY9j5vaQCRU5tUln7L7PW.lJIqP4rK', 'dBkV+tP4gFIffg8UsQvgFm1YZDVvQ2FKOW4rYUhWblBDdVdEcm9DUTBFQjZSRTlKSXQ3Q2hHQ3hEWWlRbk5MY3REN2dKRkJSK0I0ZXp1Vkw=', 'Tom1', 'info@webspell-rm.de', 1, '', '', 1, 1, 0, NULL, NULL, 0, '', NULL, NULL, NULL, 6460, 0, 0, 'email', NULL, NULL, NULL, 0, NULL, NULL),
(18, '2026-03-25 13:09:09', '2026-03-25 13:09:19', '$2y$12$SeNQdBXlgkqpSjDQDbs9Wu6yiShnAvpYFHrLRJBM54i31hMARI1Oq', 'BYb05IFJxRbpg7TswT4+9HZOZUhZTnFPR2VUcS9tQ21BMEhaU2VSeTBDeENvVDlOOGx5QlpHVWNuc2cydE4xVXJwTDBySDRuSGp6czhhTXU=', 'Fjolnd', 'fjolnd@nexpell.de', 1, '', '', 1, 1, 0, NULL, NULL, 0, '', NULL, NULL, NULL, 1211, 0, 0, 'email', NULL, NULL, NULL, 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user_profiles`
--

CREATE TABLE `user_profiles` (
  `userID` int(10) UNSIGNED NOT NULL,
  `firstname` varchar(100) DEFAULT NULL,
  `lastname` varchar(100) DEFAULT NULL,
  `location` varchar(150) DEFAULT NULL,
  `about_me` text DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `birthday` date DEFAULT NULL,
  `gender` varchar(50) DEFAULT NULL,
  `signatur` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `user_profiles`
--

INSERT INTO `user_profiles` (`userID`, `firstname`, `lastname`, `location`, `about_me`, `avatar`, `birthday`, `gender`, `signatur`) VALUES
(1, '', '', '', '<a href=\"https://nexpell.de\">web</a><div><img src=\"/images/uploads/nx_editor/nx_20260307_164222_389717220c3c.jpg\" alt=\"\" style=\"width:200px\"></div>', NULL, '0000-00-00', '', '');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user_register_attempts`
--

CREATE TABLE `user_register_attempts` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `attempt_time` datetime NOT NULL DEFAULT current_timestamp(),
  `status` enum('success','failed') NOT NULL DEFAULT 'failed',
  `reason` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user_roles`
--

CREATE TABLE `user_roles` (
  `roleID` int(11) NOT NULL,
  `role_name` varchar(50) NOT NULL,
  `modulname` varchar(100) NOT NULL DEFAULT '',
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_default` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `user_roles`
--

INSERT INTO `user_roles` (`roleID`, `role_name`, `modulname`, `description`, `is_active`, `is_default`) VALUES
(1, 'Admin', 'ac_admin', 'Vollzugriff', 1, 0),
(2, 'Co-Admin', 'ac_coadmin', 'Unterstützt Admin', 1, 0),
(3, 'Leader', 'ac_leader', 'Clan-Leiter', 1, 0),
(4, 'Co-Leader', 'ac_coleader', 'Vertretung', 1, 0),
(5, 'Squad-Leader', 'ac_squadleader', 'Squad-Leitung', 1, 0),
(6, 'War-Organisator', 'ac_warorganizer', 'Turnierorga', 1, 0),
(7, 'Moderator', 'ac_moderator', 'Moderation', 1, 0),
(8, 'Redakteur', 'ac_editor', 'News/Content', 1, 0),
(9, 'Member', 'ac_member', 'Mitglied', 1, 0),
(10, 'Trial-Member', 'ac_trialmember', 'Probezeit', 1, 0),
(11, 'Gast', 'ac_guest', 'Besucher', 1, 0),
(12, 'Registrierter Benutzer', 'ac_registered', 'Angemeldet', 1, 0),
(13, 'Ehrenmitglied', 'ac_honor', 'Ehrenstatus', 1, 0),
(14, 'Streamer', 'ac_streamer', 'Streams', 1, 0),
(15, 'Designer', 'ac_designer', 'Grafiken', 1, 0),
(16, 'Techniker', 'ac_technician', 'Technik', 1, 0);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user_role_admin_navi_rights`
--

CREATE TABLE `user_role_admin_navi_rights` (
  `id` int(11) NOT NULL,
  `roleID` int(11) NOT NULL,
  `type` enum('link','category') NOT NULL,
  `modulname` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `user_role_admin_navi_rights`
--

INSERT INTO `user_role_admin_navi_rights` (`id`, `roleID`, `type`, `modulname`) VALUES
(63, 1, 'link', 'about'),
(6, 1, 'link', 'ac_contact'),
(4, 1, 'link', 'ac_dashboard_navigation'),
(7, 1, 'link', 'ac_database'),
(12, 1, 'link', 'ac_db_stats'),
(13, 1, 'link', 'ac_editlang'),
(5, 1, 'link', 'ac_email'),
(14, 1, 'link', 'ac_headstyle'),
(11, 1, 'link', 'ac_imprint'),
(15, 1, 'link', 'ac_languages'),
(16, 1, 'link', 'ac_log_viewer'),
(1, 1, 'link', 'ac_overview'),
(17, 1, 'link', 'ac_plugin_installer'),
(18, 1, 'link', 'ac_plugin_manager'),
(19, 1, 'link', 'ac_plugin_widgets_save'),
(20, 1, 'link', 'ac_plugin_widgets_setting'),
(21, 1, 'link', 'ac_privacy_policy'),
(22, 1, 'link', 'ac_security_overview'),
(23, 1, 'link', 'ac_seo_meta'),
(3, 1, 'link', 'ac_settings'),
(24, 1, 'link', 'ac_site_lock'),
(9, 1, 'link', 'ac_startpage'),
(10, 1, 'link', 'ac_static'),
(25, 1, 'link', 'ac_statistic'),
(26, 1, 'link', 'ac_stylesheet'),
(48, 1, 'link', 'ac_terms_of_service'),
(8, 1, 'link', 'ac_theme'),
(27, 1, 'link', 'ac_theme_installer'),
(28, 1, 'link', 'ac_theme_preview'),
(29, 1, 'link', 'ac_theme_save'),
(30, 1, 'link', 'ac_update_core'),
(31, 1, 'link', 'ac_user_roles'),
(2, 1, 'link', 'ac_visitor_statistic'),
(32, 1, 'link', 'ac_webside_navigation'),
(62, 1, 'link', 'achievements'),
(77, 1, 'link', 'articles'),
(64, 1, 'link', 'carousel'),
(68, 1, 'link', 'counter'),
(71, 1, 'link', 'discord'),
(72, 1, 'link', 'downloads'),
(33, 1, 'link', 'footer'),
(106, 1, 'link', 'forum'),
(101, 1, 'link', 'gallery'),
(78, 1, 'link', 'gametracker'),
(102, 1, 'link', 'joinus'),
(96, 1, 'link', 'lastlogin'),
(108, 1, 'link', 'links'),
(100, 1, 'link', 'live_visitor'),
(103, 1, 'link', 'masterlist'),
(97, 1, 'link', 'messenger'),
(47, 1, 'link', 'navigation'),
(98, 1, 'link', 'news'),
(69, 1, 'link', 'partners'),
(104, 1, 'link', 'pricing'),
(105, 1, 'link', 'raidplaner'),
(60, 1, 'link', 'rules'),
(107, 1, 'link', 'search'),
(99, 1, 'link', 'shoutbox'),
(81, 1, 'link', 'sponsors'),
(74, 1, 'link', 'teamspeak'),
(80, 1, 'link', 'todo'),
(73, 1, 'link', 'twitch'),
(67, 1, 'link', 'userlist'),
(79, 1, 'link', 'youtube'),
(34, 1, 'category', 'cat_content'),
(35, 1, 'category', 'cat_design'),
(36, 1, 'category', 'cat_media'),
(37, 1, 'category', 'cat_partners'),
(38, 1, 'category', 'cat_plugins'),
(39, 1, 'category', 'cat_security'),
(40, 1, 'category', 'cat_slider_header'),
(41, 1, 'category', 'cat_social'),
(42, 1, 'category', 'cat_statistics'),
(43, 1, 'category', 'cat_system'),
(44, 1, 'category', 'cat_team'),
(45, 1, 'category', 'cat_tools_game'),
(46, 1, 'category', 'cat_users');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user_role_assignments`
--

CREATE TABLE `user_role_assignments` (
  `assignmentID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `roleID` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `assigned_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `user_role_assignments`
--

INSERT INTO `user_role_assignments` (`assignmentID`, `userID`, `roleID`, `created_at`, `assigned_at`) VALUES
(1, 1, 1, '2026-03-02 15:47:03', '2026-03-02 15:47:03'),
(2, 1, 9, '2026-03-07 13:28:38', '2026-03-07 13:28:38'),
(3, 2, 12, '2026-03-08 13:37:14', '2026-03-08 13:37:14'),
(4, 3, 12, '2026-03-16 18:52:41', '2026-03-16 18:52:41'),
(5, 2, 9, '2026-03-21 18:25:25', '2026-03-21 18:25:25'),
(6, 3, 9, '2026-03-21 18:25:31', '2026-03-21 18:25:31'),
(21, 18, 12, '2026-03-25 13:09:09', '2026-03-25 13:09:09');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user_sessions`
--

CREATE TABLE `user_sessions` (
  `id` int(11) NOT NULL,
  `session_id` varchar(255) NOT NULL,
  `userID` int(11) NOT NULL,
  `user_ip` varchar(45) DEFAULT NULL,
  `session_data` text DEFAULT NULL,
  `browser` text DEFAULT NULL,
  `last_activity` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `user_sessions`
--

INSERT INTO `user_sessions` (`id`, `session_id`, `userID`, `user_ip`, `session_data`, `browser`, `last_activity`) VALUES
(1, '4e907c319f835cd80f2e495513562f7a', 1, '94.31.75.0', 'a:34:{s:8:\"language\";s:2:\"de\";s:10:\"csrf_token\";s:64:\"3de972fc5189c7c64d3570e26cb8bbd65137ee0dad74ef81f0058c028025489d\";s:4:\"lang\";s:2:\"de\";s:16:\"license_accepted\";b:1;s:7:\"db_data\";a:5:{s:7:\"DB_HOST\";s:9:\"localhost\";s:7:\"DB_USER\";s:8:\"d0453787\";s:7:\"DB_PASS\";s:20:\"wBVbroMxsojJvukLxxJ9\";s:7:\"DB_NAME\";s:8:\"d0453787\";s:7:\"AES_KEY\";s:32:\"M8qr4MgzLLzATHL1nvPakveTsLZ4xnZt\";}s:17:\"install_adminuser\";s:7:\"T-Seven\";s:17:\"install_adminmail\";s:15:\"info@nexpell.de\";s:17:\"install_adminpass\";s:60:\"$2y$12$F6eQoyEmyANOl.Z3CmH1Hug6grf6y0iu0qXUL.ZQ9qJwz8xYMAsOq\";s:19:\"install_adminpepper\";s:108:\"DmBUCfWaHwejpJDblP8Q00hNSkN1K3VuMzJlczFVd3gxL2NOVmNnVDgxV3V3K1V2ZEFtc1E0Q01QeVA2aWZOL3hHSHdBY2N1WnpuNlpjSW4=\";s:19:\"install_adminweburl\";s:23:\"https://test.nexpell.de\";s:19:\"admin_user_inserted\";b:1;s:6:\"userID\";i:1;s:8:\"username\";s:7:\"T-Seven\";s:5:\"email\";s:15:\"info@nexpell.de\";s:5:\"roles\";a:1:{i:0;i:1;}s:10:\"role_names\";a:1:{i:0;s:5:\"Admin\";}s:8:\"is_admin\";b:1;s:10:\"is_coadmin\";b:0;s:9:\"is_leader\";b:0;s:11:\"is_coleader\";b:0;s:14:\"is_squadleader\";b:0;s:9:\"is_warorg\";b:0;s:12:\"is_moderator\";b:0;s:9:\"is_editor\";b:0;s:9:\"is_member\";b:0;s:8:\"is_trial\";b:0;s:8:\"is_guest\";b:0;s:13:\"is_registered\";b:0;s:8:\"is_honor\";b:0;s:11:\"is_streamer\";b:0;s:11:\"is_designer\";b:0;s:13:\"is_technician\";b:0;s:6:\"roleID\";i:1;s:8:\"userrole\";s:5:\"admin\";}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0', 1772466449),
(2, '8633ea31687fe0fb8c50ac68af2c1da5', 1, '94.31.75.0', 'a:25:{s:8:\"language\";s:2:\"de\";s:10:\"csrf_token\";s:64:\"15ea36be8d855a0c1b3d6dc80d71353b9f3c83ad21d5c791483992a4f969670c\";s:6:\"userID\";i:1;s:8:\"username\";s:7:\"T-Seven\";s:5:\"email\";s:15:\"info@nexpell.de\";s:5:\"roles\";a:1:{i:0;i:1;}s:10:\"role_names\";a:1:{i:0;s:5:\"Admin\";}s:8:\"is_admin\";b:1;s:10:\"is_coadmin\";b:0;s:9:\"is_leader\";b:0;s:11:\"is_coleader\";b:0;s:14:\"is_squadleader\";b:0;s:9:\"is_warorg\";b:0;s:12:\"is_moderator\";b:0;s:9:\"is_editor\";b:0;s:9:\"is_member\";b:0;s:8:\"is_trial\";b:0;s:8:\"is_guest\";b:0;s:13:\"is_registered\";b:0;s:8:\"is_honor\";b:0;s:11:\"is_streamer\";b:0;s:11:\"is_designer\";b:0;s:13:\"is_technician\";b:0;s:6:\"roleID\";i:1;s:8:\"userrole\";s:5:\"admin\";}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0', 1772466459),
(3, 'b90b9e0ea91b939c1e83df7ba8c1b9d5', 1, '94.31.75.0', 'a:25:{s:8:\"language\";s:2:\"de\";s:10:\"csrf_token\";s:64:\"721f80bcd53fea2d7b0ea94b3fe1f99086c11bb0881721832663c7e104bd2c91\";s:6:\"userID\";i:1;s:8:\"username\";s:7:\"T-Seven\";s:5:\"email\";s:15:\"info@nexpell.de\";s:5:\"roles\";a:1:{i:0;i:1;}s:10:\"role_names\";a:1:{i:0;s:5:\"Admin\";}s:8:\"is_admin\";b:1;s:10:\"is_coadmin\";b:0;s:9:\"is_leader\";b:0;s:11:\"is_coleader\";b:0;s:14:\"is_squadleader\";b:0;s:9:\"is_warorg\";b:0;s:12:\"is_moderator\";b:0;s:9:\"is_editor\";b:0;s:9:\"is_member\";b:0;s:8:\"is_trial\";b:0;s:8:\"is_guest\";b:0;s:13:\"is_registered\";b:0;s:8:\"is_honor\";b:0;s:11:\"is_streamer\";b:0;s:11:\"is_designer\";b:0;s:13:\"is_technician\";b:0;s:6:\"roleID\";i:1;s:8:\"userrole\";s:5:\"admin\";}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0', 1772466555),
(4, 'b890088450c3de21d7b406a4107e70a3', 1, '94.31.75.0', 'a:25:{s:8:\"language\";s:2:\"de\";s:10:\"csrf_token\";s:64:\"91e7b0105d4a7ecc8da621de3aa850d4406439f49f5772a678f5bf1ae1593c89\";s:6:\"userID\";i:1;s:8:\"username\";s:7:\"T-Seven\";s:5:\"email\";s:15:\"info@nexpell.de\";s:5:\"roles\";a:1:{i:0;i:1;}s:10:\"role_names\";a:1:{i:0;s:5:\"Admin\";}s:8:\"is_admin\";b:1;s:10:\"is_coadmin\";b:0;s:9:\"is_leader\";b:0;s:11:\"is_coleader\";b:0;s:14:\"is_squadleader\";b:0;s:9:\"is_warorg\";b:0;s:12:\"is_moderator\";b:0;s:9:\"is_editor\";b:0;s:9:\"is_member\";b:0;s:8:\"is_trial\";b:0;s:8:\"is_guest\";b:0;s:13:\"is_registered\";b:0;s:8:\"is_honor\";b:0;s:11:\"is_streamer\";b:0;s:11:\"is_designer\";b:0;s:13:\"is_technician\";b:0;s:6:\"roleID\";i:1;s:8:\"userrole\";s:5:\"admin\";}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0', 1772467057),
(5, 'c2cc05f54cc15999c785d0af315baf81', 1, '94.31.75.0', 'a:25:{s:8:\"language\";s:2:\"de\";s:10:\"csrf_token\";s:64:\"792cbd0f95af6ff926ed153821a2a0ed1018e11f2b8de35d030cb5e615ce0826\";s:6:\"userID\";i:1;s:8:\"username\";s:7:\"T-Seven\";s:5:\"email\";s:15:\"info@nexpell.de\";s:5:\"roles\";a:1:{i:0;i:1;}s:10:\"role_names\";a:1:{i:0;s:5:\"Admin\";}s:8:\"is_admin\";b:1;s:10:\"is_coadmin\";b:0;s:9:\"is_leader\";b:0;s:11:\"is_coleader\";b:0;s:14:\"is_squadleader\";b:0;s:9:\"is_warorg\";b:0;s:12:\"is_moderator\";b:0;s:9:\"is_editor\";b:0;s:9:\"is_member\";b:0;s:8:\"is_trial\";b:0;s:8:\"is_guest\";b:0;s:13:\"is_registered\";b:0;s:8:\"is_honor\";b:0;s:11:\"is_streamer\";b:0;s:11:\"is_designer\";b:0;s:13:\"is_technician\";b:0;s:6:\"roleID\";i:1;s:8:\"userrole\";s:5:\"admin\";}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0', 1772553494),
(6, '4f14cc6dd3407bccb74df4f2f0959685', 1, '94.31.75.0', 'a:25:{s:8:\"language\";s:2:\"de\";s:10:\"csrf_token\";s:64:\"535c3b669ed257bd554a0b3bcaf984947ee2bdb3c4604c1e02ff959a4fc59719\";s:6:\"userID\";i:1;s:8:\"username\";s:7:\"T-Seven\";s:5:\"email\";s:15:\"info@nexpell.de\";s:5:\"roles\";a:1:{i:0;i:1;}s:10:\"role_names\";a:1:{i:0;s:5:\"Admin\";}s:8:\"is_admin\";b:1;s:10:\"is_coadmin\";b:0;s:9:\"is_leader\";b:0;s:11:\"is_coleader\";b:0;s:14:\"is_squadleader\";b:0;s:9:\"is_warorg\";b:0;s:12:\"is_moderator\";b:0;s:9:\"is_editor\";b:0;s:9:\"is_member\";b:0;s:8:\"is_trial\";b:0;s:8:\"is_guest\";b:0;s:13:\"is_registered\";b:0;s:8:\"is_honor\";b:0;s:11:\"is_streamer\";b:0;s:11:\"is_designer\";b:0;s:13:\"is_technician\";b:0;s:6:\"roleID\";i:1;s:8:\"userrole\";s:5:\"admin\";}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0', 1772644844),
(7, '75fb78aa31eb5ec3e7fdc487c63ed5d7', 1, '94.31.75.0', 'a:25:{s:8:\"language\";s:2:\"de\";s:10:\"csrf_token\";s:64:\"31f49005c03b0a8b7ee7cf4efe21e9cd0215d78442b73d040aece3cb077a7c5e\";s:6:\"userID\";i:1;s:8:\"username\";s:7:\"T-Seven\";s:5:\"email\";s:15:\"info@nexpell.de\";s:5:\"roles\";a:1:{i:0;i:1;}s:10:\"role_names\";a:1:{i:0;s:5:\"Admin\";}s:8:\"is_admin\";b:1;s:10:\"is_coadmin\";b:0;s:9:\"is_leader\";b:0;s:11:\"is_coleader\";b:0;s:14:\"is_squadleader\";b:0;s:9:\"is_warorg\";b:0;s:12:\"is_moderator\";b:0;s:9:\"is_editor\";b:0;s:9:\"is_member\";b:0;s:8:\"is_trial\";b:0;s:8:\"is_guest\";b:0;s:13:\"is_registered\";b:0;s:8:\"is_honor\";b:0;s:11:\"is_streamer\";b:0;s:11:\"is_designer\";b:0;s:13:\"is_technician\";b:0;s:6:\"roleID\";i:1;s:8:\"userrole\";s:5:\"admin\";}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0', 1772737176),
(8, 'c02f84d3e5680f9dd58247b1c89f6ff2', 1, '94.31.75.0', 'a:25:{s:8:\"language\";s:2:\"de\";s:10:\"csrf_token\";s:64:\"20353e8f0a5ff9bf2c9efe8f869e7f577b73247e9fdb01441e57ec2c3c4bed00\";s:6:\"userID\";i:1;s:8:\"username\";s:7:\"T-Seven\";s:5:\"email\";s:15:\"info@nexpell.de\";s:5:\"roles\";a:1:{i:0;i:1;}s:10:\"role_names\";a:1:{i:0;s:5:\"Admin\";}s:8:\"is_admin\";b:1;s:10:\"is_coadmin\";b:0;s:9:\"is_leader\";b:0;s:11:\"is_coleader\";b:0;s:14:\"is_squadleader\";b:0;s:9:\"is_warorg\";b:0;s:12:\"is_moderator\";b:0;s:9:\"is_editor\";b:0;s:9:\"is_member\";b:0;s:8:\"is_trial\";b:0;s:8:\"is_guest\";b:0;s:13:\"is_registered\";b:0;s:8:\"is_honor\";b:0;s:11:\"is_streamer\";b:0;s:11:\"is_designer\";b:0;s:13:\"is_technician\";b:0;s:6:\"roleID\";i:1;s:8:\"userrole\";s:5:\"admin\";}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0', 1772823749),
(9, '453eebc9e2ad8187c234a635f0c732fb', 1, '94.31.75.0', 'a:27:{s:8:\"language\";s:2:\"de\";s:10:\"csrf_token\";s:64:\"be1f731a3baf8a3beb55cdcf21b70a7457ef98872ba5f1d055519d88178733b9\";s:23:\"twitch_api_access_token\";s:30:\"b3ttixl4q005nug9pd6v8o5juipwl3\";s:31:\"twitch_api_access_token_expires\";i:1778552563;s:6:\"userID\";i:1;s:8:\"username\";s:7:\"T-Seven\";s:5:\"email\";s:15:\"info@nexpell.de\";s:5:\"roles\";a:1:{i:0;i:1;}s:10:\"role_names\";a:1:{i:0;s:5:\"Admin\";}s:8:\"is_admin\";b:1;s:10:\"is_coadmin\";b:0;s:9:\"is_leader\";b:0;s:11:\"is_coleader\";b:0;s:14:\"is_squadleader\";b:0;s:9:\"is_warorg\";b:0;s:12:\"is_moderator\";b:0;s:9:\"is_editor\";b:0;s:9:\"is_member\";b:0;s:8:\"is_trial\";b:0;s:8:\"is_guest\";b:0;s:13:\"is_registered\";b:0;s:8:\"is_honor\";b:0;s:11:\"is_streamer\";b:0;s:11:\"is_designer\";b:0;s:13:\"is_technician\";b:0;s:6:\"roleID\";i:1;s:8:\"userrole\";s:5:\"admin\";}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0', 1772876516),
(10, 'd7b150a8feb194c41219af4a83db6fb5', 1, '94.31.75.0', 'a:25:{s:8:\"language\";s:2:\"de\";s:10:\"csrf_token\";s:64:\"864e42e4e8df305dca790222fdfc09c4e89fca7c6e954cf5db81fca9e4f7bcd4\";s:6:\"userID\";i:1;s:8:\"username\";s:7:\"T-Seven\";s:5:\"email\";s:15:\"info@nexpell.de\";s:5:\"roles\";a:2:{i:0;i:1;i:1;i:9;}s:10:\"role_names\";a:2:{i:0;s:5:\"Admin\";i:1;s:6:\"Member\";}s:8:\"is_admin\";b:1;s:10:\"is_coadmin\";b:0;s:9:\"is_leader\";b:0;s:11:\"is_coleader\";b:0;s:14:\"is_squadleader\";b:0;s:9:\"is_warorg\";b:0;s:12:\"is_moderator\";b:0;s:9:\"is_editor\";b:0;s:9:\"is_member\";b:1;s:8:\"is_trial\";b:0;s:8:\"is_guest\";b:0;s:13:\"is_registered\";b:0;s:8:\"is_honor\";b:0;s:11:\"is_streamer\";b:0;s:11:\"is_designer\";b:0;s:13:\"is_technician\";b:0;s:6:\"roleID\";i:1;s:8:\"userrole\";s:5:\"admin\";}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0', 1772962632),
(11, 'd75da5771cedbc87dc6cf57ee0048b6c', 2, '94.31.75.0', 'a:25:{s:8:\"language\";s:2:\"de\";s:10:\"csrf_token\";s:64:\"15220672630ba728d44ac9aa0ff38db81f3e3b41a5238ac806f17c2af8432453\";s:6:\"userID\";i:2;s:8:\"username\";s:4:\"Tom1\";s:5:\"email\";s:22:\"t-seven@webspell-rm.de\";s:5:\"roles\";a:1:{i:0;i:12;}s:10:\"role_names\";a:1:{i:0;s:22:\"Registrierter Benutzer\";}s:8:\"is_admin\";b:0;s:10:\"is_coadmin\";b:0;s:9:\"is_leader\";b:0;s:11:\"is_coleader\";b:0;s:14:\"is_squadleader\";b:0;s:9:\"is_warorg\";b:0;s:12:\"is_moderator\";b:0;s:9:\"is_editor\";b:0;s:9:\"is_member\";b:0;s:8:\"is_trial\";b:0;s:8:\"is_guest\";b:0;s:13:\"is_registered\";b:1;s:8:\"is_honor\";b:0;s:11:\"is_streamer\";b:0;s:11:\"is_designer\";b:0;s:13:\"is_technician\";b:0;s:6:\"roleID\";i:12;s:8:\"userrole\";s:4:\"user\";}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 1772977072),
(12, '7934a7921e00e078b7fd4da029d0863e', 2, '94.31.75.0', 'a:25:{s:8:\"language\";s:2:\"de\";s:10:\"csrf_token\";s:64:\"ea6a64c09bd215aedbe6052d1aa64f5f811e7ebaa564a1421ad62df5508f914f\";s:6:\"userID\";i:2;s:8:\"username\";s:5:\"Lucas\";s:5:\"email\";s:22:\"t-seven@webspell-rm.de\";s:5:\"roles\";a:1:{i:0;i:12;}s:10:\"role_names\";a:1:{i:0;s:22:\"Registrierter Benutzer\";}s:8:\"is_admin\";b:0;s:10:\"is_coadmin\";b:0;s:9:\"is_leader\";b:0;s:11:\"is_coleader\";b:0;s:14:\"is_squadleader\";b:0;s:9:\"is_warorg\";b:0;s:12:\"is_moderator\";b:0;s:9:\"is_editor\";b:0;s:9:\"is_member\";b:0;s:8:\"is_trial\";b:0;s:8:\"is_guest\";b:0;s:13:\"is_registered\";b:1;s:8:\"is_honor\";b:0;s:11:\"is_streamer\";b:0;s:11:\"is_designer\";b:0;s:13:\"is_technician\";b:0;s:6:\"roleID\";i:12;s:8:\"userrole\";s:4:\"user\";}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 1772987541),
(13, '3bac44e8f730e3f6013ee73d7ae0e35f', 1, '94.31.75.0', 'a:25:{s:8:\"language\";s:2:\"de\";s:10:\"csrf_token\";s:64:\"9db6a2ef609a88a59ec569d71bf820c787d39c0f97f68bcc99c8aa0b3f5f6c79\";s:6:\"userID\";i:1;s:8:\"username\";s:7:\"T-Seven\";s:5:\"email\";s:15:\"info@nexpell.de\";s:5:\"roles\";a:2:{i:0;i:1;i:1;i:9;}s:10:\"role_names\";a:2:{i:0;s:5:\"Admin\";i:1;s:6:\"Member\";}s:8:\"is_admin\";b:1;s:10:\"is_coadmin\";b:0;s:9:\"is_leader\";b:0;s:11:\"is_coleader\";b:0;s:14:\"is_squadleader\";b:0;s:9:\"is_warorg\";b:0;s:12:\"is_moderator\";b:0;s:9:\"is_editor\";b:0;s:9:\"is_member\";b:1;s:8:\"is_trial\";b:0;s:8:\"is_guest\";b:0;s:13:\"is_registered\";b:0;s:8:\"is_honor\";b:0;s:11:\"is_streamer\";b:0;s:11:\"is_designer\";b:0;s:13:\"is_technician\";b:0;s:6:\"roleID\";i:1;s:8:\"userrole\";s:5:\"admin\";}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0', 1773074482),
(14, 'ecc4892036fe3686d7c8029248b5c32a', 1, '94.31.75.0', 'a:25:{s:8:\"language\";s:2:\"de\";s:10:\"csrf_token\";s:64:\"1f82098727ad1b61c28575fe79c0640e3ea4980dd39fcc8eb745fda08aa3f203\";s:6:\"userID\";i:1;s:8:\"username\";s:7:\"T-Seven\";s:5:\"email\";s:15:\"info@nexpell.de\";s:5:\"roles\";a:2:{i:0;i:1;i:1;i:9;}s:10:\"role_names\";a:2:{i:0;s:5:\"Admin\";i:1;s:6:\"Member\";}s:8:\"is_admin\";b:1;s:10:\"is_coadmin\";b:0;s:9:\"is_leader\";b:0;s:11:\"is_coleader\";b:0;s:14:\"is_squadleader\";b:0;s:9:\"is_warorg\";b:0;s:12:\"is_moderator\";b:0;s:9:\"is_editor\";b:0;s:9:\"is_member\";b:1;s:8:\"is_trial\";b:0;s:8:\"is_guest\";b:0;s:13:\"is_registered\";b:0;s:8:\"is_honor\";b:0;s:11:\"is_streamer\";b:0;s:11:\"is_designer\";b:0;s:13:\"is_technician\";b:0;s:6:\"roleID\";i:1;s:8:\"userrole\";s:5:\"admin\";}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0', 1773159567),
(15, 'c9b05c082f3a95b5f3cda00bd7c8bf69', 1, '94.31.75.0', 'a:25:{s:8:\"language\";s:2:\"de\";s:10:\"csrf_token\";s:64:\"a8732a9cce90ca4288d3a748d210fffdef302f3633e0c84f14929a36ceb6aa54\";s:6:\"userID\";i:1;s:8:\"username\";s:7:\"T-Seven\";s:5:\"email\";s:15:\"info@nexpell.de\";s:5:\"roles\";a:2:{i:0;i:1;i:1;i:9;}s:10:\"role_names\";a:2:{i:0;s:5:\"Admin\";i:1;s:6:\"Member\";}s:8:\"is_admin\";b:1;s:10:\"is_coadmin\";b:0;s:9:\"is_leader\";b:0;s:11:\"is_coleader\";b:0;s:14:\"is_squadleader\";b:0;s:9:\"is_warorg\";b:0;s:12:\"is_moderator\";b:0;s:9:\"is_editor\";b:0;s:9:\"is_member\";b:1;s:8:\"is_trial\";b:0;s:8:\"is_guest\";b:0;s:13:\"is_registered\";b:0;s:8:\"is_honor\";b:0;s:11:\"is_streamer\";b:0;s:11:\"is_designer\";b:0;s:13:\"is_technician\";b:0;s:6:\"roleID\";i:1;s:8:\"userrole\";s:5:\"admin\";}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0', 1773248636),
(16, 'e69d7bd0d8cbacc93bec18073eb87bfa', 1, '94.31.75.0', 'a:25:{s:8:\"language\";s:2:\"de\";s:10:\"csrf_token\";s:64:\"8b4ffbf8122c09c832b5d287468d8882f9661eaafeff6fb679665c11a6a05b5e\";s:6:\"userID\";i:1;s:8:\"username\";s:7:\"T-Seven\";s:5:\"email\";s:15:\"info@nexpell.de\";s:5:\"roles\";a:2:{i:0;i:1;i:1;i:9;}s:10:\"role_names\";a:2:{i:0;s:5:\"Admin\";i:1;s:6:\"Member\";}s:8:\"is_admin\";b:1;s:10:\"is_coadmin\";b:0;s:9:\"is_leader\";b:0;s:11:\"is_coleader\";b:0;s:14:\"is_squadleader\";b:0;s:9:\"is_warorg\";b:0;s:12:\"is_moderator\";b:0;s:9:\"is_editor\";b:0;s:9:\"is_member\";b:1;s:8:\"is_trial\";b:0;s:8:\"is_guest\";b:0;s:13:\"is_registered\";b:0;s:8:\"is_honor\";b:0;s:11:\"is_streamer\";b:0;s:11:\"is_designer\";b:0;s:13:\"is_technician\";b:0;s:6:\"roleID\";i:1;s:8:\"userrole\";s:5:\"admin\";}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0', 1773336663),
(17, 'a68f5f4fe7da70c3ad44f86ec50e4e23', 1, '94.31.75.0', 'a:25:{s:8:\"language\";s:2:\"de\";s:10:\"csrf_token\";s:64:\"2e40cdb8fcda50dfa618ef8e25e7e224e08031163ee356ad4d19fd9c2b9aa6fa\";s:6:\"userID\";i:1;s:8:\"username\";s:7:\"T-Seven\";s:5:\"email\";s:15:\"info@nexpell.de\";s:5:\"roles\";a:2:{i:0;i:1;i:1;i:9;}s:10:\"role_names\";a:2:{i:0;s:5:\"Admin\";i:1;s:6:\"Member\";}s:8:\"is_admin\";b:1;s:10:\"is_coadmin\";b:0;s:9:\"is_leader\";b:0;s:11:\"is_coleader\";b:0;s:14:\"is_squadleader\";b:0;s:9:\"is_warorg\";b:0;s:12:\"is_moderator\";b:0;s:9:\"is_editor\";b:0;s:9:\"is_member\";b:1;s:8:\"is_trial\";b:0;s:8:\"is_guest\";b:0;s:13:\"is_registered\";b:0;s:8:\"is_honor\";b:0;s:11:\"is_streamer\";b:0;s:11:\"is_designer\";b:0;s:13:\"is_technician\";b:0;s:6:\"roleID\";i:1;s:8:\"userrole\";s:5:\"admin\";}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0', 1773485256),
(18, '8cb159f1d1e2262e4c830d1f791c4511', 1, '94.31.75.0', 'a:25:{s:8:\"language\";s:2:\"de\";s:10:\"csrf_token\";s:64:\"9cac9278de557369b63b90b33f21f8d75b399cf487554a7b830c6f8be72810bb\";s:6:\"userID\";i:1;s:8:\"username\";s:7:\"T-Seven\";s:5:\"email\";s:15:\"info@nexpell.de\";s:5:\"roles\";a:2:{i:0;i:1;i:1;i:9;}s:10:\"role_names\";a:2:{i:0;s:5:\"Admin\";i:1;s:6:\"Member\";}s:8:\"is_admin\";b:1;s:10:\"is_coadmin\";b:0;s:9:\"is_leader\";b:0;s:11:\"is_coleader\";b:0;s:14:\"is_squadleader\";b:0;s:9:\"is_warorg\";b:0;s:12:\"is_moderator\";b:0;s:9:\"is_editor\";b:0;s:9:\"is_member\";b:1;s:8:\"is_trial\";b:0;s:8:\"is_guest\";b:0;s:13:\"is_registered\";b:0;s:8:\"is_honor\";b:0;s:11:\"is_streamer\";b:0;s:11:\"is_designer\";b:0;s:13:\"is_technician\";b:0;s:6:\"roleID\";i:1;s:8:\"userrole\";s:5:\"admin\";}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0', 1773580276),
(19, 'e052301d8888c03259756d55f64f5390', 1, '94.31.75.0', 'a:25:{s:8:\"language\";s:2:\"de\";s:10:\"csrf_token\";s:64:\"f58a6f65affc11a042f939dd28dd315f02ea252080a97bc3645668e2482290ca\";s:6:\"userID\";i:1;s:8:\"username\";s:7:\"T-Seven\";s:5:\"email\";s:15:\"info@nexpell.de\";s:5:\"roles\";a:2:{i:0;i:1;i:1;i:9;}s:10:\"role_names\";a:2:{i:0;s:5:\"Admin\";i:1;s:6:\"Member\";}s:8:\"is_admin\";b:1;s:10:\"is_coadmin\";b:0;s:9:\"is_leader\";b:0;s:11:\"is_coleader\";b:0;s:14:\"is_squadleader\";b:0;s:9:\"is_warorg\";b:0;s:12:\"is_moderator\";b:0;s:9:\"is_editor\";b:0;s:9:\"is_member\";b:1;s:8:\"is_trial\";b:0;s:8:\"is_guest\";b:0;s:13:\"is_registered\";b:0;s:8:\"is_honor\";b:0;s:11:\"is_streamer\";b:0;s:11:\"is_designer\";b:0;s:13:\"is_technician\";b:0;s:6:\"roleID\";i:1;s:8:\"userrole\";s:5:\"admin\";}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0', 1773674131),
(20, '68c8bf99215d536f56ddcb1d51bcaa00', 2, '94.31.75.0', 'a:25:{s:8:\"language\";s:2:\"de\";s:10:\"csrf_token\";s:64:\"1d30b06b69fcab08b299fcc4a85b35df0be567deb05375e9cab7eb412a48d2b1\";s:6:\"userID\";i:2;s:8:\"username\";s:5:\"Lucas\";s:5:\"email\";s:22:\"t-seven@webspell-rm.de\";s:5:\"roles\";a:1:{i:0;i:12;}s:10:\"role_names\";a:1:{i:0;s:22:\"Registrierter Benutzer\";}s:8:\"is_admin\";b:0;s:10:\"is_coadmin\";b:0;s:9:\"is_leader\";b:0;s:11:\"is_coleader\";b:0;s:14:\"is_squadleader\";b:0;s:9:\"is_warorg\";b:0;s:12:\"is_moderator\";b:0;s:9:\"is_editor\";b:0;s:9:\"is_member\";b:0;s:8:\"is_trial\";b:0;s:8:\"is_guest\";b:0;s:13:\"is_registered\";b:1;s:8:\"is_honor\";b:0;s:11:\"is_streamer\";b:0;s:11:\"is_designer\";b:0;s:13:\"is_technician\";b:0;s:6:\"roleID\";i:12;s:8:\"userrole\";s:4:\"user\";}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 1773685782),
(21, '0d4eaf7c64e8f31da669247fd48feee8', 3, '94.31.75.0', 'a:26:{s:8:\"language\";s:2:\"de\";s:13:\"nx_form_guard\";a:2:{s:7:\"contact\";i:1773686882;s:8:\"register\";i:1773687161;}s:10:\"csrf_token\";s:64:\"da0484199358ffa4d2f46d70841064959da254f89cfd6c2034786a4e5bed0c98\";s:6:\"userID\";i:3;s:8:\"username\";s:4:\"Tom1\";s:5:\"email\";s:19:\"info@webspell-rm.de\";s:5:\"roles\";a:1:{i:0;i:12;}s:10:\"role_names\";a:1:{i:0;s:22:\"Registrierter Benutzer\";}s:8:\"is_admin\";b:0;s:10:\"is_coadmin\";b:0;s:9:\"is_leader\";b:0;s:11:\"is_coleader\";b:0;s:14:\"is_squadleader\";b:0;s:9:\"is_warorg\";b:0;s:12:\"is_moderator\";b:0;s:9:\"is_editor\";b:0;s:9:\"is_member\";b:0;s:8:\"is_trial\";b:0;s:8:\"is_guest\";b:0;s:13:\"is_registered\";b:1;s:8:\"is_honor\";b:0;s:11:\"is_streamer\";b:0;s:11:\"is_designer\";b:0;s:13:\"is_technician\";b:0;s:6:\"roleID\";i:12;s:8:\"userrole\";s:4:\"user\";}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0', 1773687266),
(22, '1304de5cfb5fb6154899a6615784373e', 1, '94.31.75.0', 'a:25:{s:8:\"language\";s:2:\"de\";s:10:\"csrf_token\";s:64:\"f5f935b0cc65ec9c4bc5f2cef4a668826435931ce01803125969d1d8bee604f3\";s:6:\"userID\";i:1;s:8:\"username\";s:7:\"T-Seven\";s:5:\"email\";s:15:\"info@nexpell.de\";s:5:\"roles\";a:2:{i:0;i:1;i:1;i:9;}s:10:\"role_names\";a:2:{i:0;s:5:\"Admin\";i:1;s:6:\"Member\";}s:8:\"is_admin\";b:1;s:10:\"is_coadmin\";b:0;s:9:\"is_leader\";b:0;s:11:\"is_coleader\";b:0;s:14:\"is_squadleader\";b:0;s:9:\"is_warorg\";b:0;s:12:\"is_moderator\";b:0;s:9:\"is_editor\";b:0;s:9:\"is_member\";b:1;s:8:\"is_trial\";b:0;s:8:\"is_guest\";b:0;s:13:\"is_registered\";b:0;s:8:\"is_honor\";b:0;s:11:\"is_streamer\";b:0;s:11:\"is_designer\";b:0;s:13:\"is_technician\";b:0;s:6:\"roleID\";i:1;s:8:\"userrole\";s:5:\"admin\";}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0', 1773693762),
(23, 'ceceb54564b3fcc08c0a424ffcb9f61a', 1, '94.31.75.0', 'a:25:{s:8:\"language\";s:2:\"de\";s:10:\"csrf_token\";s:64:\"dd4826ceef97a714021bdbbd0cc67586f63ca24ae658e7cf0ace9bc301cf5b39\";s:6:\"userID\";i:1;s:8:\"username\";s:7:\"T-Seven\";s:5:\"email\";s:15:\"info@nexpell.de\";s:5:\"roles\";a:2:{i:0;i:1;i:1;i:9;}s:10:\"role_names\";a:2:{i:0;s:5:\"Admin\";i:1;s:6:\"Member\";}s:8:\"is_admin\";b:1;s:10:\"is_coadmin\";b:0;s:9:\"is_leader\";b:0;s:11:\"is_coleader\";b:0;s:14:\"is_squadleader\";b:0;s:9:\"is_warorg\";b:0;s:12:\"is_moderator\";b:0;s:9:\"is_editor\";b:0;s:9:\"is_member\";b:1;s:8:\"is_trial\";b:0;s:8:\"is_guest\";b:0;s:13:\"is_registered\";b:0;s:8:\"is_honor\";b:0;s:11:\"is_streamer\";b:0;s:11:\"is_designer\";b:0;s:13:\"is_technician\";b:0;s:6:\"roleID\";i:1;s:8:\"userrole\";s:5:\"admin\";}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0', 1773766041),
(24, '39b810f354a5e341061c21b477800b6f', 1, '94.31.75.0', 'a:25:{s:8:\"language\";s:2:\"de\";s:10:\"csrf_token\";s:64:\"27056dd94777861213d55da137aa7004db9a2746e6bbb2ed9b212ba9ed8d0b53\";s:6:\"userID\";i:1;s:8:\"username\";s:7:\"T-Seven\";s:5:\"email\";s:15:\"info@nexpell.de\";s:5:\"roles\";a:2:{i:0;i:1;i:1;i:9;}s:10:\"role_names\";a:2:{i:0;s:5:\"Admin\";i:1;s:6:\"Member\";}s:8:\"is_admin\";b:1;s:10:\"is_coadmin\";b:0;s:9:\"is_leader\";b:0;s:11:\"is_coleader\";b:0;s:14:\"is_squadleader\";b:0;s:9:\"is_warorg\";b:0;s:12:\"is_moderator\";b:0;s:9:\"is_editor\";b:0;s:9:\"is_member\";b:1;s:8:\"is_trial\";b:0;s:8:\"is_guest\";b:0;s:13:\"is_registered\";b:0;s:8:\"is_honor\";b:0;s:11:\"is_streamer\";b:0;s:11:\"is_designer\";b:0;s:13:\"is_technician\";b:0;s:6:\"roleID\";i:1;s:8:\"userrole\";s:5:\"admin\";}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0', 1773859979),
(25, 'faa3e0f4909db8fe6b928484bc4b96b9', 1, '94.31.75.0', 'a:24:{s:8:\"language\";s:2:\"de\";s:6:\"userID\";i:1;s:8:\"username\";s:7:\"T-Seven\";s:5:\"email\";s:15:\"info@nexpell.de\";s:5:\"roles\";a:2:{i:0;i:1;i:1;i:9;}s:10:\"role_names\";a:2:{i:0;s:5:\"Admin\";i:1;s:6:\"Member\";}s:8:\"is_admin\";b:1;s:10:\"is_coadmin\";b:0;s:9:\"is_leader\";b:0;s:11:\"is_coleader\";b:0;s:14:\"is_squadleader\";b:0;s:9:\"is_warorg\";b:0;s:12:\"is_moderator\";b:0;s:9:\"is_editor\";b:0;s:9:\"is_member\";b:1;s:8:\"is_trial\";b:0;s:8:\"is_guest\";b:0;s:13:\"is_registered\";b:0;s:8:\"is_honor\";b:0;s:11:\"is_streamer\";b:0;s:11:\"is_designer\";b:0;s:13:\"is_technician\";b:0;s:6:\"roleID\";i:1;s:8:\"userrole\";s:5:\"admin\";}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0', 1773948418),
(26, '5c423427d229e62db17eedad69395917', 1, '94.31.75.0', 'a:24:{s:8:\"language\";s:2:\"de\";s:6:\"userID\";i:1;s:8:\"username\";s:7:\"T-Seven\";s:5:\"email\";s:15:\"info@nexpell.de\";s:5:\"roles\";a:2:{i:0;i:1;i:1;i:9;}s:10:\"role_names\";a:2:{i:0;s:5:\"Admin\";i:1;s:6:\"Member\";}s:8:\"is_admin\";b:1;s:10:\"is_coadmin\";b:0;s:9:\"is_leader\";b:0;s:11:\"is_coleader\";b:0;s:14:\"is_squadleader\";b:0;s:9:\"is_warorg\";b:0;s:12:\"is_moderator\";b:0;s:9:\"is_editor\";b:0;s:9:\"is_member\";b:1;s:8:\"is_trial\";b:0;s:8:\"is_guest\";b:0;s:13:\"is_registered\";b:0;s:8:\"is_honor\";b:0;s:11:\"is_streamer\";b:0;s:11:\"is_designer\";b:0;s:13:\"is_technician\";b:0;s:6:\"roleID\";i:1;s:8:\"userrole\";s:5:\"admin\";}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0', 1774026896),
(27, '4dac8cad0d6e6bf0fb577700808c2c42', 1, '94.31.75.0', 'a:25:{s:8:\"language\";s:2:\"de\";s:10:\"csrf_token\";s:64:\"886d625793414b94647200d01c4f63ed5e0d4e6e138145eafa27ea90912cfd64\";s:6:\"userID\";i:1;s:8:\"username\";s:7:\"T-Seven\";s:5:\"email\";s:15:\"info@nexpell.de\";s:5:\"roles\";a:2:{i:0;i:1;i:1;i:9;}s:10:\"role_names\";a:2:{i:0;s:5:\"Admin\";i:1;s:6:\"Member\";}s:8:\"is_admin\";b:1;s:10:\"is_coadmin\";b:0;s:9:\"is_leader\";b:0;s:11:\"is_coleader\";b:0;s:14:\"is_squadleader\";b:0;s:9:\"is_warorg\";b:0;s:12:\"is_moderator\";b:0;s:9:\"is_editor\";b:0;s:9:\"is_member\";b:1;s:8:\"is_trial\";b:0;s:8:\"is_guest\";b:0;s:13:\"is_registered\";b:0;s:8:\"is_honor\";b:0;s:11:\"is_streamer\";b:0;s:11:\"is_designer\";b:0;s:13:\"is_technician\";b:0;s:6:\"roleID\";i:1;s:8:\"userrole\";s:5:\"admin\";}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0', 1774092730),
(28, '6dbc6afe8f4a0535ff074b2ff83c3954', 1, '94.31.75.0', 'a:25:{s:8:\"language\";s:2:\"de\";s:10:\"csrf_token\";s:64:\"8ea4ab63a92cda936e48acb7b6080648dfaaf838036f2c2e29ae490b8dc0ec0e\";s:6:\"userID\";i:1;s:8:\"username\";s:7:\"T-Seven\";s:5:\"email\";s:15:\"info@nexpell.de\";s:5:\"roles\";a:2:{i:0;i:1;i:1;i:9;}s:10:\"role_names\";a:2:{i:0;s:5:\"Admin\";i:1;s:6:\"Member\";}s:8:\"is_admin\";b:1;s:10:\"is_coadmin\";b:0;s:9:\"is_leader\";b:0;s:11:\"is_coleader\";b:0;s:14:\"is_squadleader\";b:0;s:9:\"is_warorg\";b:0;s:12:\"is_moderator\";b:0;s:9:\"is_editor\";b:0;s:9:\"is_member\";b:1;s:8:\"is_trial\";b:0;s:8:\"is_guest\";b:0;s:13:\"is_registered\";b:0;s:8:\"is_honor\";b:0;s:11:\"is_streamer\";b:0;s:11:\"is_designer\";b:0;s:13:\"is_technician\";b:0;s:6:\"roleID\";i:1;s:8:\"userrole\";s:5:\"admin\";}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0', 1774174741),
(29, 'e307074752d222fe05e0148885adf8aa', 1, '94.31.75.0', 'a:25:{s:8:\"language\";s:2:\"de\";s:10:\"csrf_token\";s:64:\"52c9aa583cc62045643c58d9d92ef0dbb6877ab59707bc56623f2dfa6a8f4d04\";s:6:\"userID\";i:1;s:8:\"username\";s:7:\"T-Seven\";s:5:\"email\";s:15:\"info@nexpell.de\";s:5:\"roles\";a:2:{i:0;i:1;i:1;i:9;}s:10:\"role_names\";a:2:{i:0;s:5:\"Admin\";i:1;s:6:\"Member\";}s:8:\"is_admin\";b:1;s:10:\"is_coadmin\";b:0;s:9:\"is_leader\";b:0;s:11:\"is_coleader\";b:0;s:14:\"is_squadleader\";b:0;s:9:\"is_warorg\";b:0;s:12:\"is_moderator\";b:0;s:9:\"is_editor\";b:0;s:9:\"is_member\";b:1;s:8:\"is_trial\";b:0;s:8:\"is_guest\";b:0;s:13:\"is_registered\";b:0;s:8:\"is_honor\";b:0;s:11:\"is_streamer\";b:0;s:11:\"is_designer\";b:0;s:13:\"is_technician\";b:0;s:6:\"roleID\";i:1;s:8:\"userrole\";s:5:\"admin\";}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0', 1774394663),
(30, 'ed5967db917098bd7606b7bb8facb104', 1, '94.31.75.0', 'a:25:{s:8:\"language\";s:2:\"de\";s:10:\"csrf_token\";s:64:\"881d5e5f6b8f83d000c343f4f99ec0a0e5693fa20dd9bbc2dc27088063c70b8a\";s:6:\"userID\";i:1;s:8:\"username\";s:7:\"T-Seven\";s:5:\"email\";s:15:\"info@nexpell.de\";s:5:\"roles\";a:2:{i:0;i:1;i:1;i:9;}s:10:\"role_names\";a:2:{i:0;s:5:\"Admin\";i:1;s:6:\"Member\";}s:8:\"is_admin\";b:1;s:10:\"is_coadmin\";b:0;s:9:\"is_leader\";b:0;s:11:\"is_coleader\";b:0;s:14:\"is_squadleader\";b:0;s:9:\"is_warorg\";b:0;s:12:\"is_moderator\";b:0;s:9:\"is_editor\";b:0;s:9:\"is_member\";b:1;s:8:\"is_trial\";b:0;s:8:\"is_guest\";b:0;s:13:\"is_registered\";b:0;s:8:\"is_honor\";b:0;s:11:\"is_streamer\";b:0;s:11:\"is_designer\";b:0;s:13:\"is_technician\";b:0;s:6:\"roleID\";i:1;s:8:\"userrole\";s:5:\"admin\";}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0', 1774430381),
(31, 'c4b2de336121cf8193b38821ae78c1b9', 1, '94.31.75.0', 'a:25:{s:8:\"language\";s:2:\"de\";s:10:\"csrf_token\";s:64:\"c4e6ac0e6368e2b22c6fa4d74951991f17b51d59b1094c659d60514882e911db\";s:6:\"userID\";i:1;s:8:\"username\";s:7:\"T-Seven\";s:5:\"email\";s:15:\"info@nexpell.de\";s:5:\"roles\";a:2:{i:0;i:1;i:1;i:9;}s:10:\"role_names\";a:2:{i:0;s:5:\"Admin\";i:1;s:6:\"Member\";}s:8:\"is_admin\";b:1;s:10:\"is_coadmin\";b:0;s:9:\"is_leader\";b:0;s:11:\"is_coleader\";b:0;s:14:\"is_squadleader\";b:0;s:9:\"is_warorg\";b:0;s:12:\"is_moderator\";b:0;s:9:\"is_editor\";b:0;s:9:\"is_member\";b:1;s:8:\"is_trial\";b:0;s:8:\"is_guest\";b:0;s:13:\"is_registered\";b:0;s:8:\"is_honor\";b:0;s:11:\"is_streamer\";b:0;s:11:\"is_designer\";b:0;s:13:\"is_technician\";b:0;s:6:\"roleID\";i:1;s:8:\"userrole\";s:5:\"admin\";}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0', 1774432195),
(32, 'c235f6c857ccaddc265896ae25960e9a', 17, '94.31.75.0', 'a:26:{s:8:\"language\";s:2:\"de\";s:10:\"csrf_token\";s:64:\"437bc25baf5e1801830c3a5e6ef5805efe59dd9ed180ffce3337d8064812ec33\";s:3:\"2fa\";a:4:{s:7:\"user_id\";i:17;s:5:\"email\";s:17:\"fjolnd@nexpell.de\";s:6:\"method\";s:5:\"email\";s:5:\"nonce\";s:32:\"8b33640483d5a7908b9b8072c00348d8\";}s:6:\"userID\";i:17;s:8:\"username\";s:6:\"Fjolnd\";s:5:\"email\";s:17:\"fjolnd@nexpell.de\";s:5:\"roles\";a:1:{i:0;i:12;}s:10:\"role_names\";a:1:{i:0;s:22:\"Registrierter Benutzer\";}s:8:\"is_admin\";b:0;s:10:\"is_coadmin\";b:0;s:9:\"is_leader\";b:0;s:11:\"is_coleader\";b:0;s:14:\"is_squadleader\";b:0;s:9:\"is_warorg\";b:0;s:12:\"is_moderator\";b:0;s:9:\"is_editor\";b:0;s:9:\"is_member\";b:0;s:8:\"is_trial\";b:0;s:8:\"is_guest\";b:0;s:13:\"is_registered\";b:1;s:8:\"is_honor\";b:0;s:11:\"is_streamer\";b:0;s:11:\"is_designer\";b:0;s:13:\"is_technician\";b:0;s:6:\"roleID\";i:12;s:8:\"userrole\";s:4:\"user\";}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 1774438261),
(33, '41743e665a6091b004452104ef7781f7', 18, '94.31.75.0', 'a:25:{s:8:\"language\";s:2:\"de\";s:10:\"csrf_token\";s:64:\"45afa98a83ea1bc497ab9146ebb609b74fbd8666d4b68e803691467738705cee\";s:6:\"userID\";i:18;s:8:\"username\";s:6:\"Fjolnd\";s:5:\"email\";s:17:\"fjolnd@nexpell.de\";s:5:\"roles\";a:1:{i:0;i:12;}s:10:\"role_names\";a:1:{i:0;s:22:\"Registrierter Benutzer\";}s:8:\"is_admin\";b:0;s:10:\"is_coadmin\";b:0;s:9:\"is_leader\";b:0;s:11:\"is_coleader\";b:0;s:14:\"is_squadleader\";b:0;s:9:\"is_warorg\";b:0;s:12:\"is_moderator\";b:0;s:9:\"is_editor\";b:0;s:9:\"is_member\";b:0;s:8:\"is_trial\";b:0;s:8:\"is_guest\";b:0;s:13:\"is_registered\";b:1;s:8:\"is_honor\";b:0;s:11:\"is_streamer\";b:0;s:11:\"is_designer\";b:0;s:13:\"is_technician\";b:0;s:6:\"roleID\";i:12;s:8:\"userrole\";s:4:\"user\";}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 1774444159);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user_settings`
--

CREATE TABLE `user_settings` (
  `userID` int(10) UNSIGNED NOT NULL,
  `language` varchar(10) DEFAULT 'de',
  `dark_mode` tinyint(1) DEFAULT 0,
  `email_notifications` tinyint(1) DEFAULT 1,
  `private_profile` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `user_settings`
--

INSERT INTO `user_settings` (`userID`, `language`, `dark_mode`, `email_notifications`, `private_profile`) VALUES
(1, 'de', 0, 1, 0);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user_socials`
--

CREATE TABLE `user_socials` (
  `userID` int(10) UNSIGNED NOT NULL,
  `facebook` varchar(255) DEFAULT NULL,
  `twitter` varchar(255) DEFAULT NULL,
  `instagram` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `github` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `user_socials`
--

INSERT INTO `user_socials` (`userID`, `facebook`, `twitter`, `instagram`, `website`, `github`) VALUES
(1, '', '', '', 'www.nexpell.de', '');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user_stats`
--

CREATE TABLE `user_stats` (
  `userID` int(10) UNSIGNED NOT NULL,
  `points` int(10) UNSIGNED DEFAULT 0,
  `lastlogin` datetime DEFAULT NULL,
  `registerdate` datetime DEFAULT current_timestamp(),
  `logins_count` int(10) UNSIGNED DEFAULT 0,
  `total_time_online` int(10) UNSIGNED DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user_username`
--

CREATE TABLE `user_username` (
  `userID` int(11) NOT NULL,
  `username` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `user_username`
--

INSERT INTO `user_username` (`userID`, `username`) VALUES
(1, 'T-Seven'),
(3, 'Tom1'),
(5, 'Fjolnd'),
(6, 'Fjolnd'),
(7, 'Fjolnd'),
(8, 'Fjolnd'),
(9, 'Fjolnd'),
(12, 'Fjolnd'),
(13, 'Fjolnd'),
(14, 'Tom3'),
(15, 'Fjolnd');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `visitors_live`
--

CREATE TABLE `visitors_live` (
  `id` int(11) NOT NULL,
  `time` int(11) NOT NULL,
  `userID` int(11) DEFAULT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `site` varchar(255) DEFAULT NULL,
  `country_code` varchar(5) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `visitors_live`
--

INSERT INTO `visitors_live` (`id`, `time`, `userID`, `ip`, `site`, `country_code`, `user_agent`) VALUES
(36236, 1774449885, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `visitors_live_history`
--

CREATE TABLE `visitors_live_history` (
  `id` int(10) UNSIGNED NOT NULL,
  `time` int(10) UNSIGNED NOT NULL,
  `userID` int(10) UNSIGNED DEFAULT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `site` varchar(255) DEFAULT NULL,
  `country_code` char(2) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `visitors_live_history`
--

INSERT INTO `visitors_live_history` (`id`, `time`, `userID`, `ip`, `site`, `country_code`, `user_agent`) VALUES
(2533, 1774394652, NULL, '94.31.75.87', '/', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2534, 1774394663, 1, '94.31.75.87', '/', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2535, 1774394725, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2536, 1774394786, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2537, 1774394846, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2538, 1774394906, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2539, 1774394967, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2540, 1774395027, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2541, 1774395088, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2542, 1774395148, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2543, 1774395208, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2544, 1774395269, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2545, 1774395329, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2546, 1774395390, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2547, 1774395450, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2548, 1774395511, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2549, 1774395571, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2550, 1774395632, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2551, 1774395693, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2552, 1774395754, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2553, 1774395814, 1, '94.31.75.87', '/includes/plugins/footer/css/footer.css', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2554, 1774395880, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2555, 1774423478, NULL, '84.163.153.33', '/', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2556, 1774430368, NULL, '94.31.75.87', '/', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2557, 1774430381, 1, '94.31.75.87', '/', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2558, 1774430442, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2559, 1774430503, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2560, 1774430563, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2561, 1774430624, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2562, 1774430685, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2563, 1774430746, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2564, 1774430806, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2565, 1774430867, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2566, 1774430927, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2567, 1774430988, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2568, 1774431048, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2569, 1774431109, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2570, 1774431169, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2571, 1774431230, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2572, 1774431291, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2573, 1774431352, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2574, 1774431413, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2575, 1774431473, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2576, 1774431533, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2577, 1774431593, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2578, 1774431653, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2579, 1774431714, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2580, 1774431774, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2581, 1774431835, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2582, 1774431895, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2583, 1774431955, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2584, 1774432016, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2585, 1774432076, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2586, 1774432137, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2587, 1774432189, NULL, '94.31.75.87', '/', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2588, 1774432206, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2589, 1774432267, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2590, 1774432327, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2591, 1774432388, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2592, 1774432448, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2593, 1774432509, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2594, 1774432569, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2595, 1774432630, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2596, 1774432690, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2597, 1774432751, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2598, 1774432812, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2599, 1774432873, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2600, 1774432933, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2601, 1774432993, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2602, 1774433054, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2603, 1774433115, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2604, 1774433175, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2605, 1774433236, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2606, 1774433296, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2607, 1774433357, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2608, 1774433417, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2609, 1774433478, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2610, 1774433539, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2611, 1774433599, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2612, 1774433660, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2613, 1774433721, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2614, 1774433781, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2615, 1774433842, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2616, 1774433902, 1, '94.31.75.87', '/admin/admincenter.php?site=user_roles&action=user_create', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2617, 1774433963, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2618, 1774434024, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2619, 1774434085, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2620, 1774434145, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2621, 1774434206, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2622, 1774434266, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2623, 1774434326, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2624, 1774434387, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2625, 1774434447, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2626, 1774434508, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2627, 1774434568, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2628, 1774434629, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2629, 1774434690, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2630, 1774434751, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2631, 1774434812, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2632, 1774434872, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2633, 1774434933, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2634, 1774434993, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2635, 1774435054, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2636, 1774435114, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2637, 1774435175, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2638, 1774435235, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2639, 1774435296, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2640, 1774435356, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2641, 1774435417, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2642, 1774435477, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2643, 1774435537, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2644, 1774436851, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2645, 1774436912, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2646, 1774436972, 1, '94.31.75.87', '/admin/admincenter.php?site=user_roles&action=user_create', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2647, 1774437033, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2648, 1774437093, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2649, 1774437154, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2650, 1774437214, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2651, 1774437274, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2652, 1774437334, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2653, 1774437394, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2654, 1774437454, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2655, 1774437515, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2656, 1774437575, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2657, 1774437636, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2658, 1774437678, NULL, '94.31.75.87', '/de/register', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2659, 1774437697, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2660, 1774437757, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2661, 1774437818, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2662, 1774437879, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2663, 1774437938, NULL, '94.31.75.87', '/de/login', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2664, 1774437939, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2665, 1774438000, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2666, 1774438061, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2667, 1774438121, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2668, 1774438182, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2669, 1774438242, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2670, 1774438246, NULL, '94.31.75.87', '/de/login2fa', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2671, 1774438261, 17, '94.31.75.87', '/', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2672, 1774438303, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2673, 1774438321, 17, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2674, 1774438363, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2675, 1774438418, 17, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2676, 1774438423, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2677, 1774438478, 17, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2678, 1774438484, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2679, 1774438538, 17, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2680, 1774438544, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2681, 1774438598, 17, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2682, 1774438604, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2683, 1774438658, 17, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2684, 1774438664, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2685, 1774438718, 17, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2686, 1774438725, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2687, 1774438778, 17, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2688, 1774438785, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2689, 1774438838, 17, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2690, 1774438845, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2691, 1774438901, 17, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2692, 1774438906, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2693, 1774438961, 17, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2694, 1774438966, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2695, 1774439021, 17, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2696, 1774439026, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2697, 1774439081, 17, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2698, 1774439087, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2699, 1774439141, 17, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2700, 1774439147, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2701, 1774439201, 17, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2702, 1774439207, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2703, 1774439267, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2704, 1774439318, 17, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2705, 1774439327, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2706, 1774439378, 17, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2707, 1774439388, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2708, 1774439438, 17, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2709, 1774439448, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2710, 1774439498, 17, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2711, 1774439508, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2712, 1774439558, 17, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2713, 1774439569, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2714, 1774439618, 17, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2715, 1774439629, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2716, 1774439681, 17, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2717, 1774439689, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2718, 1774439741, 17, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2719, 1774439750, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2720, 1774439810, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2721, 1774439858, 17, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2722, 1774439871, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2723, 1774439918, 17, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2724, 1774439931, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2725, 1774439978, 17, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2726, 1774439992, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2727, 1774440038, 17, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2728, 1774440052, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2729, 1774440098, 17, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2730, 1774440113, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2731, 1774440158, 17, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2732, 1774440173, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2733, 1774440218, 17, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2734, 1774440233, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2735, 1774440279, 17, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2736, 1774440294, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2737, 1774440354, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2738, 1774440398, 17, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2739, 1774440415, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2740, 1774440458, 17, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2741, 1774440475, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2742, 1774440518, 17, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2743, 1774440536, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2744, 1774440578, 17, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2745, 1774440596, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2746, 1774440638, 17, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2747, 1774440657, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2748, 1774440698, 17, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2749, 1774440717, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2750, 1774440758, 17, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2751, 1774440778, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2752, 1774440818, 17, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2753, 1774440838, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2754, 1774440878, 17, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2755, 1774440899, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2756, 1774440938, 17, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2757, 1774440959, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2758, 1774440998, 17, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2759, 1774441020, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2760, 1774441058, 17, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2761, 1774441080, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2762, 1774441118, 17, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2763, 1774441140, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2764, 1774441178, 17, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2765, 1774441201, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2766, 1774441238, 17, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2767, 1774441261, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2768, 1774441298, 17, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2769, 1774441322, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2770, 1774441358, 17, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2771, 1774441382, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2772, 1774441418, 17, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2773, 1774441443, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2774, 1774441478, 17, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2775, 1774441503, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2776, 1774441538, 17, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2777, 1774441564, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2778, 1774441598, 17, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2779, 1774441624, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2780, 1774441658, 17, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2781, 1774441685, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2782, 1774441718, 17, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2783, 1774441745, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2784, 1774441778, 17, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2785, 1774441806, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2786, 1774441838, 17, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2787, 1774441866, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2788, 1774441898, 17, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2789, 1774441927, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2790, 1774441958, 17, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2791, 1774441987, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2792, 1774442018, 17, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2793, 1774442048, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2794, 1774442078, 17, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2795, 1774442108, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2796, 1774442138, 17, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2797, 1774442169, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2798, 1774442198, 17, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2799, 1774442229, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2800, 1774442258, 17, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2801, 1774442290, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2802, 1774442318, 17, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2803, 1774442350, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2804, 1774442378, 17, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2805, 1774442411, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2806, 1774442438, 17, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2807, 1774442471, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0');
INSERT INTO `visitors_live_history` (`id`, `time`, `userID`, `ip`, `site`, `country_code`, `user_agent`) VALUES
(2808, 1774442498, 17, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2809, 1774442532, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2810, 1774442558, 17, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2811, 1774442592, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2812, 1774442618, 17, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2813, 1774442653, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2814, 1774442678, 17, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2815, 1774442713, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2816, 1774442738, 17, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2817, 1774442774, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2818, 1774442798, 17, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2819, 1774442834, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2820, 1774442858, 17, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2821, 1774442895, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2822, 1774442918, 17, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2823, 1774442955, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2824, 1774442978, 17, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2825, 1774443015, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2826, 1774443038, 17, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2827, 1774443076, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2828, 1774443098, 17, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2829, 1774443136, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2830, 1774443158, 17, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2831, 1774443197, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2832, 1774443218, 17, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2833, 1774443257, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2834, 1774443278, 17, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2835, 1774443318, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2836, 1774443338, 17, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2837, 1774443378, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2838, 1774443398, 17, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2839, 1774443439, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2840, 1774443458, 17, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2841, 1774443499, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2842, 1774443518, 17, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2843, 1774443560, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2844, 1774443578, 17, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2845, 1774443620, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2846, 1774443638, 17, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2847, 1774443681, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2848, 1774443698, 17, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2849, 1774443741, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2850, 1774443759, 17, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2851, 1774443802, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2852, 1774443863, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2853, 1774443878, 17, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2854, 1774443923, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2855, 1774443938, 17, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2856, 1774443984, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2857, 1774443998, 17, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2858, 1774444044, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2859, 1774444058, 17, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2860, 1774444104, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2861, 1774444118, 17, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2862, 1774444155, NULL, '94.31.75.87', '/', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2863, 1774444159, 18, '94.31.75.87', '/', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2864, 1774444165, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2865, 1774444220, 18, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2866, 1774444225, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2867, 1774444286, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2868, 1774444291, 18, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2869, 1774444346, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2870, 1774444352, 18, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2871, 1774444406, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2872, 1774444413, 18, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2873, 1774444466, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2874, 1774444474, 18, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2875, 1774444527, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2876, 1774444535, 18, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2877, 1774444587, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2878, 1774444597, 18, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2879, 1774444647, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2880, 1774444657, 18, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2881, 1774444707, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2882, 1774444718, 18, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2883, 1774444767, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2884, 1774444779, 18, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2885, 1774444827, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2886, 1774444888, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2887, 1774444898, 18, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2888, 1774444948, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2889, 1774444958, 18, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2890, 1774445008, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2891, 1774445018, 18, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2892, 1774445068, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2893, 1774445078, 18, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2894, 1774445129, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2895, 1774445138, 18, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2896, 1774445189, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2897, 1774445198, 18, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2898, 1774445249, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2899, 1774445258, 18, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2900, 1774445309, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2901, 1774445318, 18, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2902, 1774445369, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2903, 1774445370, NULL, '94.31.75.87', '/', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2904, 1774445430, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2905, 1774445430, NULL, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2906, 1774445490, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2907, 1774445498, NULL, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2908, 1774445551, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2909, 1774445558, NULL, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2910, 1774445611, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2911, 1774445618, NULL, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2912, 1774445672, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2913, 1774445678, NULL, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2914, 1774445732, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2915, 1774445740, NULL, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2916, 1774445793, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2917, 1774445808, NULL, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2918, 1774445853, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2919, 1774445870, NULL, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2920, 1774445915, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2921, 1774445978, NULL, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2922, 1774445984, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2923, 1774446039, NULL, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2924, 1774446046, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2925, 1774446107, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2926, 1774446158, NULL, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2927, 1774446167, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2928, 1774446219, NULL, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2929, 1774446227, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2930, 1774446287, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2931, 1774446338, NULL, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2932, 1774446348, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2933, 1774446398, NULL, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2934, 1774446408, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2935, 1774446460, NULL, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2936, 1774446469, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2937, 1774446529, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2938, 1774446571, NULL, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2939, 1774446590, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2940, 1774446640, NULL, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2941, 1774446650, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2942, 1774446711, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2943, 1774446758, NULL, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2944, 1774446771, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2945, 1774446820, NULL, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2946, 1774446832, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2947, 1774446880, NULL, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2948, 1774446892, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2949, 1774446940, NULL, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2950, 1774446953, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2951, 1774447000, NULL, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2952, 1774447013, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2953, 1774447073, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2954, 1774447118, NULL, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2955, 1774447135, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2956, 1774447178, NULL, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2957, 1774447204, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2958, 1774447238, NULL, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2959, 1774447264, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2960, 1774447300, NULL, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2961, 1774447325, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2962, 1774447360, NULL, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2963, 1774447385, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2964, 1774447420, NULL, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2965, 1774447446, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2966, 1774447506, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2967, 1774447539, NULL, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2968, 1774447566, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2969, 1774447600, NULL, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2970, 1774447627, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2971, 1774447660, NULL, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2972, 1774447687, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2973, 1774447748, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2974, 1774447778, NULL, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2975, 1774447808, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2976, 1774447838, NULL, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2977, 1774447869, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2978, 1774447898, NULL, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2979, 1774447929, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2980, 1774447961, NULL, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2981, 1774447989, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2982, 1774448030, NULL, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2983, 1774448050, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2984, 1774448110, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2985, 1774448131, NULL, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2986, 1774448170, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2987, 1774448192, NULL, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2988, 1774448231, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2989, 1774448260, NULL, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2990, 1774448291, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2991, 1774448320, NULL, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2992, 1774448352, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2993, 1774448413, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2994, 1774448431, NULL, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2995, 1774448473, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2996, 1774448491, NULL, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2997, 1774448533, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(2998, 1774448551, NULL, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(2999, 1774448594, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(3000, 1774448612, NULL, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(3001, 1774448654, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(3002, 1774448673, NULL, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(3003, 1774448715, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(3004, 1774448740, NULL, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(3005, 1774448775, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(3006, 1774448836, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(3007, 1774448858, NULL, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(3008, 1774448897, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(3009, 1774448918, NULL, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(3010, 1774448957, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(3011, 1774448980, NULL, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(3012, 1774449018, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(3013, 1774449040, NULL, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(3014, 1774449079, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(3015, 1774449139, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(3016, 1774449158, NULL, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(3017, 1774449199, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(3018, 1774449220, NULL, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(3019, 1774449260, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(3020, 1774449320, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(3021, 1774449329, NULL, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(3022, 1774449381, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(3023, 1774449398, NULL, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(3024, 1774449441, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(3025, 1774449460, NULL, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(3026, 1774449502, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(3027, 1774449520, NULL, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(3028, 1774449562, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(3029, 1774449623, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(3030, 1774449639, NULL, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(3031, 1774449684, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(3032, 1774449744, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(3033, 1774449750, NULL, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(3034, 1774449804, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(3035, 1774449818, NULL, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(3036, 1774449865, 1, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(3037, 1774449880, NULL, '94.31.75.87', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `visitor_daily_counter`
--

CREATE TABLE `visitor_daily_counter` (
  `date` date NOT NULL,
  `hits` int(11) NOT NULL DEFAULT 0,
  `online` int(11) NOT NULL DEFAULT 0,
  `maxonline` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `visitor_daily_counter`
--

INSERT INTO `visitor_daily_counter` (`date`, `hits`, `online`, `maxonline`) VALUES
('2026-03-08', 7, 1, 3),
('2026-03-09', 6, 1, 1),
('2026-03-10', 3, 1, 1),
('2026-03-11', 5, 1, 1),
('2026-03-12', 5, 1, 1),
('2026-03-13', 6, 0, 0),
('2026-03-14', 5, 0, 1),
('2026-03-15', 4, 2, 2),
('2026-03-16', 7, 1, 1),
('2026-03-17', 2, 1, 1),
('2026-03-18', 3, 1, 1),
('2026-03-19', 5, 0, 1),
('2026-03-20', 3, 1, 1),
('2026-03-21', 3, 1, 1),
('2026-03-22', 5, 1, 1),
('2026-03-23', 9, 0, 1),
('2026-03-24', 1, 0, 0),
('2026-03-25', 5, 3, 3);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `visitor_daily_counter_hits`
--

CREATE TABLE `visitor_daily_counter_hits` (
  `id` int(11) NOT NULL,
  `date` date NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `ip_hash` char(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `visitor_daily_counter_hits`
--

INSERT INTO `visitor_daily_counter_hits` (`id`, `date`, `user_id`, `ip_hash`) VALUES
(55, '2026-03-08', 1, NULL),
(56, '2026-03-08', 2, NULL),
(57, '2026-03-08', NULL, '5e3e5caecad7659eae899e8b73a25058d372841a443216524695b108110c8724'),
(58, '2026-03-08', NULL, '23c565cad5285fa7b46d13b8c8aefd9f1d0b427b166d2df6fa4d13d5a3630d62'),
(59, '2026-03-08', NULL, 'c1aaac75ee0c7ebba0ef354efabd02085659de9de3b53b2bfb0cb2317f2ca80d'),
(60, '2026-03-09', NULL, '32458ead3ebf4e8fea59c6a9c306d54dcfbe2916ae7fed845d8f98e38fb9b1a5'),
(61, '2026-03-09', NULL, '5e3e5caecad7659eae899e8b73a25058d372841a443216524695b108110c8724'),
(62, '2026-03-09', NULL, '2f50b7535d43b94842f2e6f8508e2752fda529d11542148c37b208c248213c12'),
(63, '2026-03-09', NULL, '0faaf6907fff8d648adba5fe938fcfeeee167b814f70388000f7f4d3bd210914'),
(64, '2026-03-09', NULL, '23c565cad5285fa7b46d13b8c8aefd9f1d0b427b166d2df6fa4d13d5a3630d62'),
(65, '2026-03-09', 1, NULL),
(66, '2026-03-10', NULL, '5e3e5caecad7659eae899e8b73a25058d372841a443216524695b108110c8724'),
(67, '2026-03-10', NULL, '23c565cad5285fa7b46d13b8c8aefd9f1d0b427b166d2df6fa4d13d5a3630d62'),
(68, '2026-03-10', 1, NULL),
(69, '2026-03-11', NULL, '8864750ddd244c767b8010452eec32280bc3983bf43d17bf267e3f5a968fe2ef'),
(70, '2026-03-11', NULL, '630419e31f33dfec1a763cb0541a47aef85f41bdfaa814e57d220994474ef7dc'),
(71, '2026-03-11', NULL, '2e9ed291d591d056a96321a1905124a6192606f02c249cf9c632ae98ccfab4e9'),
(72, '2026-03-11', NULL, '23c565cad5285fa7b46d13b8c8aefd9f1d0b427b166d2df6fa4d13d5a3630d62'),
(73, '2026-03-11', 1, NULL),
(74, '2026-03-12', NULL, '0d3174ae07cbe321295cb2ad635435fbf5da24eb9c40c14cb2922824461d08dc'),
(75, '2026-03-12', NULL, '6b18c2be1edd757df896b5afe5970a15a1594039f1dcd3424c5a43b0a64cbc4f'),
(76, '2026-03-12', NULL, '5e3e5caecad7659eae899e8b73a25058d372841a443216524695b108110c8724'),
(77, '2026-03-12', NULL, '23c565cad5285fa7b46d13b8c8aefd9f1d0b427b166d2df6fa4d13d5a3630d62'),
(78, '2026-03-12', 1, NULL),
(79, '2026-03-13', NULL, 'adeeea0fedaaedef348ba51b0244303be1728e1dcce05cc308c0ee1284487bea'),
(80, '2026-03-13', NULL, 'f4714df03fc3fface790267cd4b924a585ad959c909fda0c553e3b87202ad864'),
(81, '2026-03-13', NULL, 'fd5a27017c956da378b66e6c04c52b3cdf05506a9f725da4b54510d8cc8d509f'),
(82, '2026-03-13', NULL, '5e3e5caecad7659eae899e8b73a25058d372841a443216524695b108110c8724'),
(83, '2026-03-13', NULL, 'a2bd2d8a7350c30ee2513176775be45d0c0cc2db4e59ae9a6b01daded9c7d27e'),
(84, '2026-03-13', NULL, '0ba9149198c24c9728e41ac73abb7f4d6b70c65a99bfcd818c0bac096f64b401'),
(85, '2026-03-14', NULL, '23c565cad5285fa7b46d13b8c8aefd9f1d0b427b166d2df6fa4d13d5a3630d62'),
(86, '2026-03-14', 1, NULL),
(87, '2026-03-14', NULL, '18845b9d2a64131dc2f4579b725afda3394f7bd4ed0ef0655f8d4d5ce94ac9ea'),
(88, '2026-03-14', NULL, '4423155dc37cad2aca95e6dacb69a601952c38db0c8a6c085a0928186f2c2d09'),
(89, '2026-03-14', NULL, '68201ea79afd67b3d2415305de6eb5e8ba331956469236e8c561ed5c39ba8bb9'),
(90, '2026-03-15', NULL, '9aafe70688c86b1474af390d2c75d8b2277e87a386f8e738dfcdf1d016bb5bb3'),
(91, '2026-03-15', NULL, '23c565cad5285fa7b46d13b8c8aefd9f1d0b427b166d2df6fa4d13d5a3630d62'),
(92, '2026-03-15', 1, NULL),
(93, '2026-03-15', NULL, 'a05f4ccfc5f69e833eb4f033b12432981d94f83e2c952f9faf8e9e2f3f59dddd'),
(94, '2026-03-16', NULL, '3f7941698b0384b680b6052f4e706ed10491192ae20cb61a2e6f1d7dd7164790'),
(95, '2026-03-16', NULL, '489e22b6fa900a0f2fde5eab2e0134e20f56244c730acf1599af7272a87e9f93'),
(96, '2026-03-16', NULL, '23c565cad5285fa7b46d13b8c8aefd9f1d0b427b166d2df6fa4d13d5a3630d62'),
(97, '2026-03-16', 1, NULL),
(98, '2026-03-16', 2, NULL),
(99, '2026-03-16', 3, NULL),
(100, '2026-03-16', NULL, '4a8af188ed1bea66f160c0e4262b9945cc269d04b566bd5777a859a1c94e5337'),
(101, '2026-03-17', NULL, '23c565cad5285fa7b46d13b8c8aefd9f1d0b427b166d2df6fa4d13d5a3630d62'),
(102, '2026-03-17', 1, NULL),
(103, '2026-03-18', NULL, '5e3e5caecad7659eae899e8b73a25058d372841a443216524695b108110c8724'),
(104, '2026-03-18', NULL, '23c565cad5285fa7b46d13b8c8aefd9f1d0b427b166d2df6fa4d13d5a3630d62'),
(105, '2026-03-18', 1, NULL),
(106, '2026-03-19', NULL, 'fbc02afd8823bda0f68ae8eb8bd14d7248f72a010953637f85fd191d988441c6'),
(107, '2026-03-19', NULL, 'd2a51177ce99f66d7cee780105b8f9568549faf57af73e705aa5cdf5e29e1038'),
(108, '2026-03-19', NULL, '23c565cad5285fa7b46d13b8c8aefd9f1d0b427b166d2df6fa4d13d5a3630d62'),
(109, '2026-03-19', 1, NULL),
(110, '2026-03-19', NULL, 'c3f68df08cccf3ee307562c92a7a07add25f55843c034243a6c178c831b86a2f'),
(111, '2026-03-20', NULL, '22e92607f372dc20b42d6fcbb1894f07e730d08186f0362f01642190160eb21c'),
(112, '2026-03-20', NULL, '23c565cad5285fa7b46d13b8c8aefd9f1d0b427b166d2df6fa4d13d5a3630d62'),
(113, '2026-03-20', 1, NULL),
(114, '2026-03-21', NULL, '23c565cad5285fa7b46d13b8c8aefd9f1d0b427b166d2df6fa4d13d5a3630d62'),
(115, '2026-03-21', 1, NULL),
(116, '2026-03-21', NULL, '5e3e5caecad7659eae899e8b73a25058d372841a443216524695b108110c8724'),
(117, '2026-03-22', 1, NULL),
(118, '2026-03-22', NULL, 'b28c7fac53bb332e33559ccdf54b39ec7da7410317ab1c6f6d05ecc36ec2659b'),
(119, '2026-03-22', NULL, '23c565cad5285fa7b46d13b8c8aefd9f1d0b427b166d2df6fa4d13d5a3630d62'),
(120, '2026-03-22', NULL, 'b76534c2b548669b4ebb58394f44e40a3637fbe048296bd6c270b95a5ca57802'),
(121, '2026-03-22', NULL, '5e3e5caecad7659eae899e8b73a25058d372841a443216524695b108110c8724'),
(122, '2026-03-23', NULL, '3f22462bf1e3a9a9ba561d8ca0bbcb0d8d330bbdb195f51e9bb61d5c22af6080'),
(123, '2026-03-23', NULL, 'ed631265406c5b2d19d57462f4b2778a932e8e80d084ccb1f376b4ccd6215c1f'),
(124, '2026-03-23', NULL, '5e3e5caecad7659eae899e8b73a25058d372841a443216524695b108110c8724'),
(125, '2026-03-23', NULL, '3e905c524dbe145347c58cb1bc9b4db01e0882599ab4a0c81719c7671b625a68'),
(126, '2026-03-23', NULL, '7fc95dcb1c37f33126144af8879d3870c05e211c373bf75ac05df2aa0b8f1e72'),
(127, '2026-03-23', NULL, 'e5801069f6e2f67adf861db62a92cf31fe0d4dd1664e6047088fa64ac1d54f01'),
(128, '2026-03-23', NULL, '84ea8e74b16cbd886c15567a246ab1bfd33d4929341b38aad4c9db36fac0dec7'),
(129, '2026-03-23', NULL, '29e9ae4362dc0e8befbfa7d9f764a6a60e811fa838a9a6c8453cfabf23f5392f'),
(130, '2026-03-23', NULL, 'da98398d40724b76b8a592221aab0b115e87f7ee2e7ee48c39d78e644c9d1e23'),
(131, '2026-03-24', NULL, '5e3e5caecad7659eae899e8b73a25058d372841a443216524695b108110c8724'),
(132, '2026-03-25', NULL, '23c565cad5285fa7b46d13b8c8aefd9f1d0b427b166d2df6fa4d13d5a3630d62'),
(133, '2026-03-25', 1, NULL),
(134, '2026-03-25', NULL, '5e3e5caecad7659eae899e8b73a25058d372841a443216524695b108110c8724'),
(135, '2026-03-25', 17, NULL),
(136, '2026-03-25', 18, NULL);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `visitor_daily_iplist`
--

CREATE TABLE `visitor_daily_iplist` (
  `id` int(11) NOT NULL,
  `dates` date NOT NULL,
  `del` int(11) NOT NULL,
  `ip` varchar(45) NOT NULL,
  `country_code` varchar(5) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `visitor_daily_iplist`
--

INSERT INTO `visitor_daily_iplist` (`id`, `dates`, `del`, `ip`, `country_code`) VALUES
(132, '2026-03-25', 1774394652, '94.31.75.87', 'de'),
(133, '2026-03-25', 1774407673, '35.85.248.130', 'us'),
(134, '2026-03-25', 1774423478, '84.163.153.33', 'de');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `visitor_daily_stats`
--

CREATE TABLE `visitor_daily_stats` (
  `id` int(11) NOT NULL,
  `date` date NOT NULL,
  `hits` int(11) NOT NULL DEFAULT 0,
  `online` int(11) NOT NULL DEFAULT 0,
  `maxonline` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `visitor_daily_stats`
--

INSERT INTO `visitor_daily_stats` (`id`, `date`, `hits`, `online`, `maxonline`) VALUES
(7, '2026-03-08', 7, 1, 3),
(8, '2026-03-09', 6, 1, 1),
(9, '2026-03-10', 3, 1, 1),
(10, '2026-03-11', 5, 1, 1),
(11, '2026-03-12', 5, 1, 1),
(12, '2026-03-13', 6, 0, 0),
(13, '2026-03-14', 5, 0, 1),
(14, '2026-03-15', 4, 2, 2),
(15, '2026-03-16', 7, 1, 1),
(16, '2026-03-17', 2, 1, 1),
(17, '2026-03-18', 3, 1, 1),
(18, '2026-03-19', 5, 0, 1),
(19, '2026-03-20', 3, 1, 1),
(20, '2026-03-21', 3, 1, 1),
(21, '2026-03-22', 5, 1, 1),
(22, '2026-03-23', 9, 0, 1),
(23, '2026-03-24', 1, 0, 0);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `visitor_statistics`
--

CREATE TABLE `visitor_statistics` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `ip_address` varchar(45) NOT NULL,
  `pageviews` int(11) DEFAULT 1,
  `last_seen` datetime NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `page` varchar(255) DEFAULT NULL,
  `country_code` varchar(10) DEFAULT NULL,
  `device_type` varchar(20) DEFAULT NULL,
  `os` varchar(50) DEFAULT NULL,
  `browser` varchar(100) DEFAULT NULL,
  `ip_hash` varchar(64) NOT NULL,
  `referer` varchar(300) NOT NULL,
  `user_agent` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `visitor_statistics`
--

INSERT INTO `visitor_statistics` (`id`, `user_id`, `ip_address`, `pageviews`, `last_seen`, `created_at`, `page`, `country_code`, `device_type`, `os`, `browser`, `ip_hash`, `referer`, `user_agent`) VALUES
(60, 1, '94.31.75.87', 173, '2026-03-08 21:47:48', '2026-03-08 18:49:18', '/index.php?site=news', 'de', 'Desktop', 'Windows', 'Firefox', '5140200416582394668fdda4c5e050fd5dfffee886b00d52bf87f9c1a584830e', 'https://www.test.nexpell.de/index.php?site=userlist', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(61, 2, '94.31.75.87', 17, '2026-03-08 20:07:58', '2026-03-08 18:49:29', '/index.php?site=logout', 'de', 'Desktop', 'Windows', 'Chrome', '041a00104cf037ac135515f1a18442c164eda9029bb3c4649b350835cf44b01d', 'https://test.nexpell.de/index.php?site=sponsors', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36'),
(62, 0, '84.163.153.33', 1, '2026-03-08 20:07:39', '2026-03-08 19:07:39', '/', 'de', 'Desktop', 'Windows', 'Firefox', '71267176c3c1eaa0edb5974619b1d4fa60ea1f45092d515c5a06577e95994e00', 'direct', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(63, 0, '94.31.75.87', 5, '2026-03-08 20:08:22', '2026-03-08 19:07:59', '/index.php?site=userlist', 'de', 'Desktop', 'Windows', 'Chrome', 'bbc3a19bb57d7f880eea126887b3c21051f4f08fcbd4ecd8f109f366175e8b50', 'https://test.nexpell.de/index.php?site=live_visitor', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36'),
(64, 0, '199.45.154.126', 1, '2026-03-08 21:44:12', '2026-03-08 20:44:12', '/', 'us', 'Desktop', 'Unknown', 'Unknown', '7befc39d07f4858facba5818d666857b04d6f04480dc70f9d4b886db1c3e4a97', 'direct', 'Mozilla/5.0 (compatible; CensysInspect/1.1; +https://about.censys.io/)'),
(65, 0, '23.180.120.131', 49, '2026-03-09 00:23:18', '2026-03-08 23:23:15', '/_next/', 'fr', 'Desktop', 'Mac', 'Chrome', '5e4596ffd439e68b6ed0b34666ff1caebc335fb1f262152d8764d04eb6259ebc', 'direct', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36'),
(66, 0, '84.163.153.33', 3, '2026-03-09 08:14:10', '2026-03-09 07:13:40', '/index.php?site=news&newsID=1', 'de', 'Desktop', 'Windows', 'Firefox', '71267176c3c1eaa0edb5974619b1d4fa60ea1f45092d515c5a06577e95994e00', 'https://test.nexpell.de/index.php?site=news', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(67, 0, '159.203.56.247', 1, '2026-03-09 13:18:29', '2026-03-09 12:18:29', '/', 'ca', 'Desktop', 'Linux', 'Chrome', 'a13b354b809799f628068b808a2e6455004628cdfe4505f8e98a2135bd4ed304', 'direct', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36'),
(68, 0, '34.29.168.99', 1, '2026-03-09 16:26:24', '2026-03-09 15:26:24', '/', 'us', 'Desktop', 'Unknown', 'Unknown', '693a2265df84748bba4d8f1755a2949d00ae4ee70b0b50feeb4ae33b3f561d2f', 'http://test.nexpell.de', 'Mozilla/5.0 (compatible; CMS-Checker/1.0; +https://example.com)'),
(69, 0, '94.31.75.87', 4, '2026-03-09 18:18:23', '2026-03-09 16:41:15', '/admin/admincenter.php?site=plugin_installer', 'de', 'Desktop', 'Windows', 'Firefox', 'bbc3a19bb57d7f880eea126887b3c21051f4f08fcbd4ecd8f109f366175e8b50', 'direct', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(70, 1, '94.31.75.87', 131, '2026-03-09 22:42:42', '2026-03-09 16:41:22', '/index.php?site=joinus', 'de', 'Desktop', 'Windows', 'Firefox', '5140200416582394668fdda4c5e050fd5dfffee886b00d52bf87f9c1a584830e', 'https://www.test.nexpell.de/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(71, 0, '84.163.153.33', 3, '2026-03-10 08:19:44', '2026-03-10 07:19:33', '/index.php?site=about', 'de', 'Desktop', 'Windows', 'Firefox', '71267176c3c1eaa0edb5974619b1d4fa60ea1f45092d515c5a06577e95994e00', 'https://test.nexpell.de/index.php?site=leistung', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(72, 0, '94.31.75.87', 7, '2026-03-10 17:19:27', '2026-03-10 16:16:38', '/index.php?site=login', 'de', 'Desktop', 'Windows', 'Firefox', 'bbc3a19bb57d7f880eea126887b3c21051f4f08fcbd4ecd8f109f366175e8b50', 'https://www.test.nexpell.de/index.php?site=login', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(73, 1, '94.31.75.87', 199, '2026-03-10 22:02:14', '2026-03-10 16:19:28', '/admin/admincenter.php?site=admin_pricing&action=edit&id=3', 'de', 'Desktop', 'Windows', 'Firefox', '5140200416582394668fdda4c5e050fd5dfffee886b00d52bf87f9c1a584830e', 'https://www.test.nexpell.de/admin/admincenter.php?site=admin_pricing', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(74, 0, '23.27.145.13', 1, '2026-03-11 10:01:34', '2026-03-11 09:01:34', '/', 'us', 'Desktop', 'Linux', 'Firefox', '1eebb611c4a31d0ab29105e1af96b3b7c8e5f2cc87847d9b4de91e901390467f', 'direct', 'Mozilla/5.0 (X11; Linux i686; rv:109.0) Gecko/20100101 Firefox/120.0'),
(75, 0, '34.134.228.25', 1, '2026-03-11 16:09:44', '2026-03-11 15:09:44', '/', 'us', 'Desktop', 'Unknown', 'Unknown', 'dbb466d99507387296e8f26809e5379c3bb695169d1c7c0bffd6f8e1f30a447f', 'http://test.nexpell.de', 'Mozilla/5.0 (compatible; CMS-Checker/1.0; +https://example.com)'),
(76, 0, '34.30.91.73', 1, '2026-03-11 16:25:17', '2026-03-11 15:25:17', '/', 'us', 'Desktop', 'Unknown', 'Unknown', '024eac220b5c74808187231e50698f3a4a9f428d13aaeacad16e3738363c9a06', 'http://www.test.nexpell.de', 'Mozilla/5.0 (compatible; CMS-Checker/1.0; +https://example.com)'),
(77, 0, '94.31.75.87', 5, '2026-03-11 21:42:46', '2026-03-11 17:03:44', '/admin/admincenter.php?site=update_core', 'de', 'Desktop', 'Windows', 'Firefox', 'bbc3a19bb57d7f880eea126887b3c21051f4f08fcbd4ecd8f109f366175e8b50', 'direct', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(78, 1, '94.31.75.87', 153, '2026-03-11 22:41:43', '2026-03-11 17:03:56', '/index.php?site=search', 'de', 'Desktop', 'Windows', 'Firefox', '5140200416582394668fdda4c5e050fd5dfffee886b00d52bf87f9c1a584830e', 'https://www.test.nexpell.de/index.php?site=index&lang=de', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(79, 0, '3.250.148.12', 1, '2026-03-12 02:48:59', '2026-03-12 01:48:59', '/', 'ie', 'Desktop', 'Unknown', 'Unknown', 'fc3d57686c72bcc2c3432720d90f91e70388730908443b525fe1ae24fd374bd4', 'direct', 'Mozilla/5.0 (compatible; NetcraftSurveyAgent/1.0; +info@netcraft.com)'),
(80, 0, '108.130.201.207', 1, '2026-03-12 10:26:49', '2026-03-12 09:26:49', '/', 'ie', 'Desktop', 'Unknown', 'Unknown', '232e9aa689327519f6020d25d2c468725cf4caea1ee507c3e4793a72f46d2147', 'direct', 'Mozilla/5.0 (compatible; NetcraftSurveyAgent/1.0; +info@netcraft.com)'),
(81, 0, '84.163.153.33', 1, '2026-03-12 10:53:25', '2026-03-12 09:53:25', '/', 'de', 'Desktop', 'Windows', 'Firefox', '71267176c3c1eaa0edb5974619b1d4fa60ea1f45092d515c5a06577e95994e00', 'direct', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(82, 0, '94.31.75.87', 6, '2026-03-12 21:39:42', '2026-03-12 17:30:57', '/index.php?site=twitch', 'de', 'Desktop', 'Windows', 'Chrome', 'bbc3a19bb57d7f880eea126887b3c21051f4f08fcbd4ecd8f109f366175e8b50', 'https://www.test.nexpell.de/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36'),
(83, 1, '94.31.75.87', 55, '2026-03-12 22:36:57', '2026-03-12 17:31:03', '/index.php?site=twitch', 'de', 'Mobile', 'iOS', 'Safari', '5140200416582394668fdda4c5e050fd5dfffee886b00d52bf87f9c1a584830e', 'https://www.test.nexpell.de/index.php?site=news', 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.0.3 Mobile/15E148 Safari/604.1'),
(84, 0, '34.241.126.38', 1, '2026-03-13 05:01:00', '2026-03-13 04:01:00', '/', 'ie', 'Desktop', 'Unknown', 'Unknown', 'cb5ebdedb34b086c463fed848c9168c9630a3f6f48b6868a6810911b21a5b5b9', 'direct', 'Mozilla/5.0 (compatible; NetcraftSurveyAgent/1.0; +info@netcraft.com)'),
(85, 0, '34.178.78.145', 1, '2026-03-13 06:15:43', '2026-03-13 05:15:43', '/', 'nl', 'Desktop', 'Mac', 'Firefox', 'b1d886f6e5f91778eb93f30a0a26a8d281d6695e5b07652971245d35642f8d89', 'direct', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 11_1; rv:115.7.0) Gecko/20100101 Firefox/115.7.0'),
(86, 0, '108.130.168.213', 1, '2026-03-13 08:17:53', '2026-03-13 07:17:53', '/', 'ie', 'Desktop', 'Unknown', 'Unknown', '55304c34b28014e4f6b2c31dfc90e5c1df70e3621ec579ebb06ad663b0ed4a80', 'direct', 'Mozilla/5.0 (compatible; NetcraftSurveyAgent/1.0; +info@netcraft.com)'),
(87, 0, '84.163.153.33', 1, '2026-03-13 10:02:52', '2026-03-13 09:02:52', '/', 'de', 'Desktop', 'Windows', 'Firefox', '71267176c3c1eaa0edb5974619b1d4fa60ea1f45092d515c5a06577e95994e00', 'direct', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(88, 0, '141.113.74.100', 45, '2026-03-13 18:13:49', '2026-03-13 17:10:41', '/index.php?site=terms_of_service', 'de', 'Desktop', 'Windows', 'Edge', 'd7bf13a19b9edaa6dc2c208f65a926c68f6740670949f7041a52b138b083e6eb', 'https://test.nexpell.de/index.php?site=imprint', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0'),
(89, 0, '34.230.220.241', 1, '2026-03-13 18:25:34', '2026-03-13 17:25:34', '/', 'us', 'Desktop', 'Linux', 'Chrome', '5f3e4601d08bd3f51117e8733ccf69b61aac57b0d72ff9e140048c34e4bccc27', 'direct', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/109.0.5414.119 Safari/537.36'),
(90, 0, '94.31.75.87', 15, '2026-03-14 14:50:35', '2026-03-14 10:39:57', '/', 'de', 'Desktop', 'Windows', 'Chrome', 'bbc3a19bb57d7f880eea126887b3c21051f4f08fcbd4ecd8f109f366175e8b50', 'direct', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36'),
(91, 1, '94.31.75.87', 227, '2026-03-14 17:48:19', '2026-03-14 10:47:36', '/index.php?site=leistung', 'de', 'Desktop', 'Windows', 'Firefox', '5140200416582394668fdda4c5e050fd5dfffee886b00d52bf87f9c1a584830e', 'https://www.test.nexpell.de/index.php?site=info', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(92, 0, '205.210.31.49', 1, '2026-03-14 13:00:02', '2026-03-14 12:00:02', '/', 'us', 'Desktop', 'Unknown', 'Unknown', '88713ca8ba9bdfd7290d8f6b8733fee047914c7375ad52c548057e02a359829e', 'direct', 'Hello from Palo Alto Networks, find out more about our scans in https://docs-cortex.paloaltonetworks.com/r/1/Cortex-Xpanse/Scanning-activity'),
(93, 0, '13.37.249.204', 4, '2026-03-14 20:58:42', '2026-03-14 19:58:42', '/', 'fr', 'Desktop', 'Mac', 'Chrome', '8b39a8c42c623b6cead9f2309686b9e58a19e6a1c20e6bdb73a37c014a3fee4a', 'direct', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36'),
(94, 0, '18.231.2.205', 4, '2026-03-14 21:24:09', '2026-03-14 20:24:09', '/', 'br', 'Desktop', 'Linux', 'Chrome', '007bd31a5504155d9c8dcf6ae7a38c673e330e0f90c38285723c63206f8620b2', 'direct', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36'),
(95, 0, '23.27.145.45', 1, '2026-03-15 10:01:31', '2026-03-15 09:01:31', '/', 'us', 'Desktop', 'Linux', 'Firefox', 'f19e1b0ca231f3d2f37f3a7674b650024a1fba5f95438e9291639463cd9004db', 'direct', 'Mozilla/5.0 (X11; Linux i686; rv:109.0) Gecko/20100101 Firefox/120.0'),
(96, 0, '94.31.75.87', 5, '2026-03-15 15:06:22', '2026-03-15 13:11:06', '/admin/admincenter.php?site=plugin_installer', 'de', 'Desktop', 'Windows', 'Firefox', 'bbc3a19bb57d7f880eea126887b3c21051f4f08fcbd4ecd8f109f366175e8b50', 'direct', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(97, 1, '94.31.75.87', 628, '2026-03-15 21:01:02', '2026-03-15 13:11:16', '/de/rules', 'de', 'Desktop', 'Windows', 'Firefox', '5140200416582394668fdda4c5e050fd5dfffee886b00d52bf87f9c1a584830e', 'direct', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(98, 0, '34.206.76.135', 1, '2026-03-15 15:11:56', '2026-03-15 14:11:56', '/', 'us', 'Desktop', 'Linux', 'Chrome', '3472160e5e2dabaacb880a704ff4a14707e00d66efa7e8c546ba5ed04485e54c', 'direct', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.0.0 Safari/537.36'),
(99, 0, '206.168.34.35', 1, '2026-03-16 02:08:45', '2026-03-16 01:08:45', '/', 'us', 'Desktop', 'Unknown', 'Unknown', 'd0cf7ee92e598a91cbad805cebbb893bb7bdf2c2d139fded1596e338dd6dbc4c', 'direct', 'Mozilla/5.0 (compatible; CensysInspect/1.1; +https://about.censys.io/)'),
(100, 0, '198.235.24.3', 1, '2026-03-16 15:21:03', '2026-03-16 14:21:03', '/', 'tw', 'Desktop', 'Unknown', 'Unknown', '96ed2b047362b0f78ebe942a0c8458cd49edd1733d18d252d6968858eaa468ba', 'direct', 'Hello from Palo Alto Networks, find out more about our scans in https://docs-cortex.paloaltonetworks.com/r/1/Cortex-Xpanse/Scanning-activity'),
(101, 0, '94.31.75.87', 233, '2026-03-16 23:03:38', '2026-03-16 15:15:23', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Desktop', 'Windows', 'Chrome', 'bbc3a19bb57d7f880eea126887b3c21051f4f08fcbd4ecd8f109f366175e8b50', 'https://www.test.nexpell.de/de/shoutbox', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36'),
(102, 1, '94.31.75.87', 28, '2026-03-16 22:26:07', '2026-03-16 15:15:31', '/de/raidplaner/my_stats', 'de', 'Desktop', 'Windows', 'Firefox', '5140200416582394668fdda4c5e050fd5dfffee886b00d52bf87f9c1a584830e', 'https://www.test.nexpell.de/de/raidplaner/calendar', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(103, 2, '94.31.75.87', 2, '2026-03-16 19:29:49', '2026-03-16 18:29:42', '/de/logout', 'de', 'Desktop', 'Windows', 'Chrome', '041a00104cf037ac135515f1a18442c164eda9029bb3c4649b350835cf44b01d', 'https://www.test.nexpell.de/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36'),
(104, 3, '94.31.75.87', 3, '2026-03-16 21:42:36', '2026-03-16 18:54:26', '/de/logout', 'de', 'Desktop', 'Windows', 'Firefox', '62a203e1d8acf12a17f51c54751b271222149a1b6cf2711776ce72fb017e87f3', 'https://www.test.nexpell.de/de/login', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(105, 0, '34.135.221.10', 1, '2026-03-16 21:35:31', '2026-03-16 20:35:31', '/', 'us', 'Desktop', 'Windows', 'Chrome', '228cf65795c95798a79007fe39de784a53f88b0212ba5f0f6e210c74820bb2ef', 'https://test.nexpell.de/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36'),
(106, 0, '94.31.75.87', 3, '2026-03-17 17:47:21', '2026-03-17 16:47:15', '/de/login', 'de', 'Desktop', 'Windows', 'Firefox', 'bbc3a19bb57d7f880eea126887b3c21051f4f08fcbd4ecd8f109f366175e8b50', 'https://www.test.nexpell.de/de/login', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(107, 1, '94.31.75.87', 1, '2026-03-17 17:47:21', '2026-03-17 16:47:21', '/', 'de', 'Desktop', 'Windows', 'Firefox', '5140200416582394668fdda4c5e050fd5dfffee886b00d52bf87f9c1a584830e', 'https://www.test.nexpell.de/de/login', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(108, 0, '84.163.153.33', 1, '2026-03-18 15:58:21', '2026-03-18 14:58:21', '/', 'de', 'Desktop', 'Windows', 'Firefox', '71267176c3c1eaa0edb5974619b1d4fa60ea1f45092d515c5a06577e95994e00', 'https://www.nexpell.de/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(109, 0, '94.31.75.0', 4, '2026-03-18 19:52:59', '2026-03-18 17:00:07', '/de/login', 'de', 'Desktop', 'Windows', 'Firefox', 'd270ca1bba1f31b12f79e6a98127b8ea983dd796e9522e589c57306e62c0da99', 'https://www.test.nexpell.de/de/login', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(110, 1, '94.31.75.0', 146, '2026-03-18 23:32:09', '2026-03-18 18:53:00', '/de/', 'de', 'Desktop', 'Windows', 'Firefox', '89a55386d78332dbf3adad17d9d51305501b1d7c3d44ea7f7dbcbc68a2eb5c0f', 'https://www.test.nexpell.de/de/profile/1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(111, 0, '45.148.10.0', 608, '2026-03-19 01:26:28', '2026-03-19 00:25:05', '/de/twitch', 'nl', 'Desktop', 'Windows', 'Chrome', 'a22998858c7900d9ece9f2e1f0619fffd1ad452094296272da9d8118ad9ca887', 'direct', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'),
(112, 0, '52.202.237.0', 1, '2026-03-19 12:25:38', '2026-03-19 11:25:38', '/', 'us', 'Desktop', 'Windows', 'Edge', 'c62906016d215265aebad94bb11309dd1ead456784b9b16f319798bcf7bf6f56', 'direct', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4093.0 Safari/537.36 Edg/83.0.470.0'),
(113, 0, '94.31.75.0', 19, '2026-03-19 20:26:58', '2026-03-19 18:55:16', '/de/login', 'de', 'Desktop', 'Windows', 'Firefox', 'd270ca1bba1f31b12f79e6a98127b8ea983dd796e9522e589c57306e62c0da99', 'https://www.test.nexpell.de/de/login', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(114, 1, '94.31.75.0', 119, '2026-03-19 22:53:17', '2026-03-19 19:26:58', '/en/youtube', 'de', 'Desktop', 'Windows', 'Firefox', '89a55386d78332dbf3adad17d9d51305501b1d7c3d44ea7f7dbcbc68a2eb5c0f', 'https://www.test.nexpell.de/en/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(115, 0, '198.235.24.0', 1, '2026-03-19 23:53:42', '2026-03-19 22:53:42', '/', 'us', 'Desktop', 'Unknown', 'Unknown', 'd482e47ca1e6101653846e866a8e34ec0d32a5db4554bc8d1ae76a5faf972a04', 'direct', 'Hello from Palo Alto Networks, find out more about our scans in https://docs-cortex.paloaltonetworks.com/r/1/Cortex-Xpanse/Scanning-activity'),
(116, 0, '52.200.222.0', 1, '2026-03-20 05:16:47', '2026-03-20 04:16:47', '/', 'us', 'Desktop', 'Windows', 'Chrome', '26667a7f9ea5af6b97a36a0709132983554645f444335b363d3dbd37d3a9a032', 'direct', 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/29.0.1547.66 Safari/537.36'),
(117, 0, '94.31.75.0', 3, '2026-03-20 18:14:55', '2026-03-20 17:14:23', '/de/login', 'de', 'Desktop', 'Windows', 'Firefox', 'd270ca1bba1f31b12f79e6a98127b8ea983dd796e9522e589c57306e62c0da99', 'https://www.test.nexpell.de/de/login', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(118, 1, '94.31.75.0', 161, '2026-03-20 23:45:33', '2026-03-20 17:14:56', '/de/', 'de', 'Desktop', 'Windows', 'Firefox', '89a55386d78332dbf3adad17d9d51305501b1d7c3d44ea7f7dbcbc68a2eb5c0f', 'https://www.test.nexpell.de/admin/admincenter.php?site=theme_designer&page=index', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(119, 0, '94.31.75.0', 3, '2026-03-21 12:32:10', '2026-03-21 11:32:02', '/de/login', 'de', 'Desktop', 'Windows', 'Firefox', 'd270ca1bba1f31b12f79e6a98127b8ea983dd796e9522e589c57306e62c0da99', 'https://www.test.nexpell.de/de/login', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(120, 1, '94.31.75.0', 6597, '2026-03-21 23:59:59', '2026-03-21 11:32:10', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Desktop', 'Windows', 'Firefox', '89a55386d78332dbf3adad17d9d51305501b1d7c3d44ea7f7dbcbc68a2eb5c0f', 'https://www.test.nexpell.de/de/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(121, 0, '84.163.153.0', 8, '2026-03-21 21:05:16', '2026-03-21 18:19:30', '/de/achievements', 'de', 'Desktop', 'Windows', 'Firefox', '76450c4471b772f309c13863be53e8dfcc77da6798692ea57f31c88f3ee99366', 'https://test.nexpell.de/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(122, 1, '94.31.75.0', 6308, '2026-03-22 20:27:35', '2026-03-21 23:00:05', '/de/news', 'de', 'Desktop', 'Windows', 'Firefox', '89a55386d78332dbf3adad17d9d51305501b1d7c3d44ea7f7dbcbc68a2eb5c0f', 'https://www.test.nexpell.de/de/youtube/page/2', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(123, 0, '52.0.247.0', 1, '2026-03-22 04:39:23', '2026-03-22 03:39:23', '/', 'us', 'Desktop', 'Windows', 'Edge', 'c4d8158b69db804011bc7875dabfed5517818dc2011ef9e194c36c1eecbd2f56', 'direct', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36 Edg/125.0.0.0'),
(124, 0, '94.31.75.0', 5, '2026-03-22 11:19:01', '2026-03-22 10:18:19', '/de/login', 'de', 'Desktop', 'Windows', 'Firefox', 'd270ca1bba1f31b12f79e6a98127b8ea983dd796e9522e589c57306e62c0da99', 'https://www.test.nexpell.de/de/login', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(125, 0, '164.90.201.0', 1, '2026-03-22 12:50:37', '2026-03-22 11:50:37', '/', 'nl', 'Desktop', 'Linux', 'Chrome', 'ba42f9b087a30e4bb24dff733c20d8074eaba3f3ca5308c41499ce44958941f3', 'direct', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36'),
(126, 0, '84.163.153.0', 10, '2026-03-22 19:16:37', '2026-03-22 17:36:19', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Desktop', 'Windows', 'Firefox', '76450c4471b772f309c13863be53e8dfcc77da6798692ea57f31c88f3ee99366', 'https://test.nexpell.de/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(127, 0, '3.82.234.0', 1, '2026-03-23 00:12:05', '2026-03-22 23:12:05', '/', 'us', 'Desktop', 'Windows', 'Chrome', 'bda42432a08a4fc91b5bcaa306fa0a116e4527e652f95adb5b59ad895d4e3993', 'direct', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/138.0.0.0 Safari/537.36'),
(128, 0, '206.168.34.0', 1, '2026-03-23 07:23:25', '2026-03-23 06:23:25', '/', 'us', 'Desktop', 'Unknown', 'Unknown', 'd36d3b02fdb0f3b618f957d8ac4d8d4f6654699388d16351acce70bb7c89eab5', 'direct', 'Mozilla/5.0 (compatible; CensysInspect/1.1; +https://about.censys.io/)'),
(129, 0, '84.163.153.0', 8, '2026-03-23 10:21:32', '2026-03-23 06:35:16', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Desktop', 'Windows', 'Firefox', '76450c4471b772f309c13863be53e8dfcc77da6798692ea57f31c88f3ee99366', 'https://test.nexpell.de/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(130, 0, '87.236.176.0', 2, '2026-03-23 23:46:30', '2026-03-23 10:58:54', '/', 'gb', 'Desktop', 'Unknown', 'Unknown', 'd393c793aabb5dafa09f8ccd21d6d8a16af8a8154f221df386440e481e5a0d20', 'http://www.test.nexpell.de', 'Mozilla/5.0 (compatible; InternetMeasurement/1.0; +https://internet-measurement.com/)'),
(131, 0, '34.79.154.0', 1, '2026-03-23 15:10:20', '2026-03-23 14:10:20', '/', 'be', 'Desktop', 'Windows', 'Chrome', 'e8eb15425a4739e248c6cd5b900e1f4a98f2234416a018d9a6b0f1cd720aa81e', 'direct', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(132, 0, '160.46.252.0', 2, '2026-03-23 15:10:22', '2026-03-23 14:10:22', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Desktop', 'Windows', 'Chrome', 'b8379789bb77a43a5cc27dd1154d086f24474188d44ed21853a2ec0867ad8c61', 'https://test.nexpell.de/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(133, 0, '137.184.76.0', 1, '2026-03-23 16:02:18', '2026-03-23 15:02:18', '/', 'us', 'Desktop', 'Linux', 'Chrome', '9dcf26e26eef4ef5de33c991cfc0ada62f2d52a6d28652307ebe1545b8143aa9', 'direct', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36'),
(134, 0, '3.139.242.0', 1, '2026-03-23 22:31:32', '2026-03-23 21:31:32', '/', 'us', 'Desktop', 'Mac', 'Chrome', '14dfe5dde80ffadee728d31fb4c872772aee018966441670b3f4bf000253d31e', 'direct', 'visionheight.com/scan Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) Chrome/126.0.0.0 Safari/537.36'),
(135, 0, '84.163.153.0', 2, '2026-03-24 08:53:38', '2026-03-24 07:53:37', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Desktop', 'Windows', 'Firefox', '76450c4471b772f309c13863be53e8dfcc77da6798692ea57f31c88f3ee99366', 'https://test.nexpell.de/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(136, 0, '94.31.75.0', 254, '2026-03-25 15:44:50', '2026-03-24 23:24:12', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Desktop', 'Windows', 'Chrome', 'd270ca1bba1f31b12f79e6a98127b8ea983dd796e9522e589c57306e62c0da99', 'https://www.test.nexpell.de/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(137, 1, '94.31.75.0', 2108, '2026-03-25 15:44:45', '2026-03-24 23:24:23', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Desktop', 'Windows', 'Firefox', '89a55386d78332dbf3adad17d9d51305501b1d7c3d44ea7f7dbcbc68a2eb5c0f', 'https://www.test.nexpell.de/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(138, 0, '84.163.153.0', 2, '2026-03-25 08:24:39', '2026-03-25 07:24:38', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Desktop', 'Windows', 'Firefox', '76450c4471b772f309c13863be53e8dfcc77da6798692ea57f31c88f3ee99366', 'https://test.nexpell.de/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'),
(139, 17, '94.31.75.0', 152, '2026-03-25 14:09:15', '2026-03-25 11:31:01', '/de/logout', 'de', 'Desktop', 'Windows', 'Chrome', '2345976ffbb698b6056d81368157dac5fda8fae167448d81671be3de1c146264', 'https://www.test.nexpell.de/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(140, 18, '94.31.75.0', 30, '2026-03-25 14:29:30', '2026-03-25 13:09:19', '/includes/plugins/shoutbox/shoutbox_ajax.php', 'de', 'Desktop', 'Windows', 'Chrome', '75aa5e03f72a20d284406efafc9e3f69fefa83baa5e92d5dd824af80c1535255', 'https://www.test.nexpell.de/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36');

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `backups`
--
ALTER TABLE `backups`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `banned_ips`
--
ALTER TABLE `banned_ips`
  ADD PRIMARY KEY (`banID`),
  ADD KEY `userID` (`userID`);

--
-- Indizes für die Tabelle `captcha`
--
ALTER TABLE `captcha`
  ADD PRIMARY KEY (`hash`);

--
-- Indizes für die Tabelle `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`commentID`),
  ADD KEY `plugin_item` (`plugin`,`itemID`),
  ADD KEY `userID` (`userID`);

--
-- Indizes für die Tabelle `contact`
--
ALTER TABLE `contact`
  ADD PRIMARY KEY (`contactID`);

--
-- Indizes für die Tabelle `failed_login_attempts`
--
ALTER TABLE `failed_login_attempts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `userID` (`userID`);

--
-- Indizes für die Tabelle `link_clicks`
--
ALTER TABLE `link_clicks`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `navigation_dashboard_categories`
--
ALTER TABLE `navigation_dashboard_categories`
  ADD PRIMARY KEY (`catID`),
  ADD UNIQUE KEY `modulname` (`modulname`);

--
-- Indizes für die Tabelle `navigation_dashboard_lang`
--
ALTER TABLE `navigation_dashboard_lang`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_content_lang` (`content_key`,`language`),
  ADD KEY `idx_modulname` (`modulname`),
  ADD KEY `idx_language` (`language`);

--
-- Indizes für die Tabelle `navigation_dashboard_links`
--
ALTER TABLE `navigation_dashboard_links`
  ADD PRIMARY KEY (`linkID`),
  ADD UNIQUE KEY `unique_modulname` (`modulname`);

--
-- Indizes für die Tabelle `navigation_website_lang`
--
ALTER TABLE `navigation_website_lang`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_content_lang` (`content_key`,`language`),
  ADD KEY `idx_modulname` (`modulname`),
  ADD KEY `idx_language` (`language`);

--
-- Indizes für die Tabelle `navigation_website_main`
--
ALTER TABLE `navigation_website_main`
  ADD PRIMARY KEY (`mnavID`),
  ADD UNIQUE KEY `unique_modulname` (`modulname`);

--
-- Indizes für die Tabelle `navigation_website_settings`
--
ALTER TABLE `navigation_website_settings`
  ADD PRIMARY KEY (`setting_key`);

--
-- Indizes für die Tabelle `navigation_website_sub`
--
ALTER TABLE `navigation_website_sub`
  ADD PRIMARY KEY (`snavID`),
  ADD UNIQUE KEY `unique_modulname_sort` (`modulname`,`sort`),
  ADD KEY `idx_mnavID` (`mnavID`);

--
-- Indizes für die Tabelle `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_token` (`token`),
  ADD KEY `idx_userID` (`userID`);

--
-- Indizes für die Tabelle `password_reset_attempts`
--
ALTER TABLE `password_reset_attempts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_ip` (`ip`);

--
-- Indizes für die Tabelle `plugins_about`
--
ALTER TABLE `plugins_about`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_content_lang` (`content_key`,`language`),
  ADD KEY `idx_content_key` (`content_key`),
  ADD KEY `idx_language` (`language`);

--
-- Indizes für die Tabelle `plugins_about_legacy`
--
ALTER TABLE `plugins_about_legacy`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `plugins_achievements`
--
ALTER TABLE `plugins_achievements`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `plugins_achievements_admin_log`
--
ALTER TABLE `plugins_achievements_admin_log`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `plugins_achievements_categories`
--
ALTER TABLE `plugins_achievements_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `plugins_achievements_settings`
--
ALTER TABLE `plugins_achievements_settings`
  ADD PRIMARY KEY (`setting_key`);

--
-- Indizes für die Tabelle `plugins_articles`
--
ALTER TABLE `plugins_articles`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `plugins_articles_categories`
--
ALTER TABLE `plugins_articles_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `plugins_articles_comments`
--
ALTER TABLE `plugins_articles_comments`
  ADD PRIMARY KEY (`commentID`),
  ADD KEY `parentID` (`parentID`),
  ADD KEY `type` (`type`),
  ADD KEY `date` (`date`);

--
-- Indizes für die Tabelle `plugins_articles_settings`
--
ALTER TABLE `plugins_articles_settings`
  ADD PRIMARY KEY (`articlessetID`);

--
-- Indizes für die Tabelle `plugins_carousel`
--
ALTER TABLE `plugins_carousel`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_type_visible_sort` (`type`,`visible`,`sort`);

--
-- Indizes für die Tabelle `plugins_carousel_lang`
--
ALTER TABLE `plugins_carousel_lang`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_content_lang` (`content_key`,`language`),
  ADD KEY `idx_content_key` (`content_key`),
  ADD KEY `idx_language` (`language`);

--
-- Indizes für die Tabelle `plugins_carousel_settings`
--
ALTER TABLE `plugins_carousel_settings`
  ADD PRIMARY KEY (`carouselID`);

--
-- Indizes für die Tabelle `plugins_discord`
--
ALTER TABLE `plugins_discord`
  ADD PRIMARY KEY (`name`);

--
-- Indizes für die Tabelle `plugins_downloads`
--
ALTER TABLE `plugins_downloads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `categoryID` (`categoryID`);

--
-- Indizes für die Tabelle `plugins_downloads_categories`
--
ALTER TABLE `plugins_downloads_categories`
  ADD PRIMARY KEY (`categoryID`);

--
-- Indizes für die Tabelle `plugins_downloads_logs`
--
ALTER TABLE `plugins_downloads_logs`
  ADD PRIMARY KEY (`logID`),
  ADD KEY `userID` (`userID`),
  ADD KEY `fileID` (`fileID`);

--
-- Indizes für die Tabelle `plugins_footer`
--
ALTER TABLE `plugins_footer`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_section` (`section_sort`,`section_title`),
  ADD KEY `idx_section_links` (`section_sort`,`section_title`,`link_sort`),
  ADD KEY `idx_footer_cat` (`row_type`,`category_key`),
  ADD KEY `idx_footer_cat_title` (`row_type`,`section_title`);

--
-- Indizes für die Tabelle `plugins_footer_lang`
--
ALTER TABLE `plugins_footer_lang`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_content_lang` (`content_key`,`language`),
  ADD KEY `idx_content_key` (`content_key`),
  ADD KEY `idx_language` (`language`);

--
-- Indizes für die Tabelle `plugins_forum_boards`
--
ALTER TABLE `plugins_forum_boards`
  ADD PRIMARY KEY (`boardID`);

--
-- Indizes für die Tabelle `plugins_forum_categories`
--
ALTER TABLE `plugins_forum_categories`
  ADD PRIMARY KEY (`catID`),
  ADD KEY `idx_boardID` (`boardID`);

--
-- Indizes für die Tabelle `plugins_forum_permissions_board`
--
ALTER TABLE `plugins_forum_permissions_board`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_board_role` (`boardID`,`role_id`),
  ADD KEY `idx_role` (`role_id`);

--
-- Indizes für die Tabelle `plugins_forum_permissions_categories`
--
ALTER TABLE `plugins_forum_permissions_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_category_role` (`catID`,`role_id`),
  ADD KEY `idx_role` (`role_id`);

--
-- Indizes für die Tabelle `plugins_forum_permissions_threads`
--
ALTER TABLE `plugins_forum_permissions_threads`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_thread_role` (`threadID`,`role_id`);

--
-- Indizes für die Tabelle `plugins_forum_posts`
--
ALTER TABLE `plugins_forum_posts`
  ADD PRIMARY KEY (`postID`),
  ADD KEY `idx_threadID` (`threadID`);

--
-- Indizes für die Tabelle `plugins_forum_post_likes`
--
ALTER TABLE `plugins_forum_post_likes`
  ADD PRIMARY KEY (`postID`,`userID`),
  ADD KEY `idx_userID` (`userID`);

--
-- Indizes für die Tabelle `plugins_forum_read`
--
ALTER TABLE `plugins_forum_read`
  ADD PRIMARY KEY (`userID`,`threadID`);

--
-- Indizes für die Tabelle `plugins_forum_threads`
--
ALTER TABLE `plugins_forum_threads`
  ADD PRIMARY KEY (`threadID`),
  ADD KEY `idx_catID` (`catID`);

--
-- Indizes für die Tabelle `plugins_forum_uploaded_images`
--
ALTER TABLE `plugins_forum_uploaded_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_userID` (`userID`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indizes für die Tabelle `plugins_gallery`
--
ALTER TABLE `plugins_gallery`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indizes für die Tabelle `plugins_gallery_categories`
--
ALTER TABLE `plugins_gallery_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `plugins_gametracker_servers`
--
ALTER TABLE `plugins_gametracker_servers`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `plugins_joinus_applications`
--
ALTER TABLE `plugins_joinus_applications`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `plugins_joinus_roles`
--
ALTER TABLE `plugins_joinus_roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_joinus_role` (`role_id`);

--
-- Indizes für die Tabelle `plugins_joinus_squads`
--
ALTER TABLE `plugins_joinus_squads`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `plugins_joinus_types`
--
ALTER TABLE `plugins_joinus_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_joinus_type` (`type_key`);

--
-- Indizes für die Tabelle `plugins_links`
--
ALTER TABLE `plugins_links`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indizes für die Tabelle `plugins_links_categories`
--
ALTER TABLE `plugins_links_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `plugins_links_settings`
--
ALTER TABLE `plugins_links_settings`
  ADD PRIMARY KEY (`linkssetID`);

--
-- Indizes für die Tabelle `plugins_messages`
--
ALTER TABLE `plugins_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `plugins_news`
--
ALTER TABLE `plugins_news`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `idx_publish_at` (`publish_at`);

--
-- Indizes für die Tabelle `plugins_news_categories`
--
ALTER TABLE `plugins_news_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indizes für die Tabelle `plugins_news_lang`
--
ALTER TABLE `plugins_news_lang`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_content_lang` (`content_key`,`language`),
  ADD KEY `idx_content_key` (`content_key`),
  ADD KEY `idx_language` (`language`);

--
-- Indizes für die Tabelle `plugins_partners`
--
ALTER TABLE `plugins_partners`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_content_lang` (`content_key`,`language`),
  ADD KEY `idx_content_key` (`content_key`),
  ADD KEY `idx_language` (`language`);

--
-- Indizes für die Tabelle `plugins_partners_settings`
--
ALTER TABLE `plugins_partners_settings`
  ADD PRIMARY KEY (`partnerssetID`);

--
-- Indizes für die Tabelle `plugins_pricing_features`
--
ALTER TABLE `plugins_pricing_features`
  ADD PRIMARY KEY (`id`),
  ADD KEY `plan_id` (`plan_id`);

--
-- Indizes für die Tabelle `plugins_pricing_plans`
--
ALTER TABLE `plugins_pricing_plans`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `plugins_raidplaner_attendance`
--
ALTER TABLE `plugins_raidplaner_attendance`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `plugins_raidplaner_bis_list`
--
ALTER TABLE `plugins_raidplaner_bis_list`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `plugins_raidplaner_bosses`
--
ALTER TABLE `plugins_raidplaner_bosses`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `plugins_raidplaner_characters`
--
ALTER TABLE `plugins_raidplaner_characters`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `plugins_raidplaner_character_gear`
--
ALTER TABLE `plugins_raidplaner_character_gear`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_char_item` (`character_id`,`item_id`);

--
-- Indizes für die Tabelle `plugins_raidplaner_classes`
--
ALTER TABLE `plugins_raidplaner_classes`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `plugins_raidplaner_events`
--
ALTER TABLE `plugins_raidplaner_events`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `plugins_raidplaner_event_bosses`
--
ALTER TABLE `plugins_raidplaner_event_bosses`
  ADD PRIMARY KEY (`event_id`,`boss_id`);

--
-- Indizes für die Tabelle `plugins_raidplaner_event_setup`
--
ALTER TABLE `plugins_raidplaner_event_setup`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `event_role_unique` (`event_id`,`role_id`);

--
-- Indizes für die Tabelle `plugins_raidplaner_items`
--
ALTER TABLE `plugins_raidplaner_items`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `plugins_raidplaner_loot_distributed`
--
ALTER TABLE `plugins_raidplaner_loot_distributed`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `plugins_raidplaner_loot_history`
--
ALTER TABLE `plugins_raidplaner_loot_history`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_event_item_char` (`event_id`,`item_id`,`character_id`);

--
-- Indizes für die Tabelle `plugins_raidplaner_participants`
--
ALTER TABLE `plugins_raidplaner_participants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `event_user_unique` (`event_id`,`userID`);

--
-- Indizes für die Tabelle `plugins_raidplaner_roles`
--
ALTER TABLE `plugins_raidplaner_roles`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `plugins_raidplaner_settings`
--
ALTER TABLE `plugins_raidplaner_settings`
  ADD PRIMARY KEY (`setting_key`);

--
-- Indizes für die Tabelle `plugins_raidplaner_setup`
--
ALTER TABLE `plugins_raidplaner_setup`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `event_role_unique` (`event_id`,`role_id`);

--
-- Indizes für die Tabelle `plugins_raidplaner_signups`
--
ALTER TABLE `plugins_raidplaner_signups`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `event_character_unique` (`event_id`,`character_id`);

--
-- Indizes für die Tabelle `plugins_raidplaner_templates`
--
ALTER TABLE `plugins_raidplaner_templates`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `plugins_raidplaner_template_bosses`
--
ALTER TABLE `plugins_raidplaner_template_bosses`
  ADD PRIMARY KEY (`template_id`,`boss_id`);

--
-- Indizes für die Tabelle `plugins_raidplaner_template_setup`
--
ALTER TABLE `plugins_raidplaner_template_setup`
  ADD PRIMARY KEY (`template_id`,`role_id`);

--
-- Indizes für die Tabelle `plugins_raidplaner_wishlists`
--
ALTER TABLE `plugins_raidplaner_wishlists`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `plugins_rules`
--
ALTER TABLE `plugins_rules`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_content_lang` (`content_key`,`language`),
  ADD KEY `idx_content_key` (`content_key`),
  ADD KEY `idx_language` (`language`);

--
-- Indizes für die Tabelle `plugins_rules_settings`
--
ALTER TABLE `plugins_rules_settings`
  ADD PRIMARY KEY (`rulessetID`);

--
-- Indizes für die Tabelle `plugins_shoutbox_messages`
--
ALTER TABLE `plugins_shoutbox_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indizes für die Tabelle `plugins_sponsors`
--
ALTER TABLE `plugins_sponsors`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `plugins_sponsors_settings`
--
ALTER TABLE `plugins_sponsors_settings`
  ADD PRIMARY KEY (`sponsorssetID`);

--
-- Indizes für die Tabelle `plugins_teamspeak`
--
ALTER TABLE `plugins_teamspeak`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `plugins_todo`
--
ALTER TABLE `plugins_todo`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_todo_assigned_to` (`assigned_to`),
  ADD KEY `idx_todo_updated_by` (`updated_by`);

--
-- Indizes für die Tabelle `plugins_twitch_banner_cache`
--
ALTER TABLE `plugins_twitch_banner_cache`
  ADD PRIMARY KEY (`channel`);

--
-- Indizes für die Tabelle `plugins_twitch_settings`
--
ALTER TABLE `plugins_twitch_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `plugins_userlist_settings`
--
ALTER TABLE `plugins_userlist_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `plugins_youtube`
--
ALTER TABLE `plugins_youtube`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `plugin_key_unique` (`plugin_name`,`setting_key`);

--
-- Indizes für die Tabelle `plugins_youtube_settings`
--
ALTER TABLE `plugins_youtube_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `ratings`
--
ALTER TABLE `ratings`
  ADD PRIMARY KEY (`ratingID`),
  ADD UNIQUE KEY `unique_vote` (`plugin`,`itemID`,`userID`),
  ADD KEY `userID` (`userID`);

--
-- Indizes für die Tabelle `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`settingID`);

--
-- Indizes für die Tabelle `settings_content_lang`
--
ALTER TABLE `settings_content_lang`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_content_lang` (`content_key`,`language`),
  ADD KEY `idx_content_key` (`content_key`),
  ADD KEY `idx_language` (`language`);

--
-- Indizes für die Tabelle `settings_headstyle_config`
--
ALTER TABLE `settings_headstyle_config`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `settings_imprint`
--
ALTER TABLE `settings_imprint`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `settings_languages`
--
ALTER TABLE `settings_languages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `iso_639_1` (`iso_639_1`);

--
-- Indizes für die Tabelle `settings_plugins`
--
ALTER TABLE `settings_plugins`
  ADD PRIMARY KEY (`pluginID`),
  ADD UNIQUE KEY `unique_modulname` (`modulname`),
  ADD UNIQUE KEY `uniq_modulname` (`modulname`);

--
-- Indizes für die Tabelle `settings_plugins_installed`
--
ALTER TABLE `settings_plugins_installed`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_modulname` (`modulname`);

--
-- Indizes für die Tabelle `settings_plugins_lang`
--
ALTER TABLE `settings_plugins_lang`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_content_lang` (`content_key`,`language`),
  ADD KEY `idx_content_key` (`content_key`),
  ADD KEY `idx_language` (`language`),
  ADD KEY `idx_modulname` (`modulname`);

--
-- Indizes für die Tabelle `settings_seo_meta_lang`
--
ALTER TABLE `settings_seo_meta_lang`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_content_lang` (`content_key`,`language`),
  ADD KEY `idx_content_key` (`content_key`),
  ADD KEY `idx_language` (`language`);

--
-- Indizes für die Tabelle `settings_site_lock`
--
ALTER TABLE `settings_site_lock`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `settings_social_media`
--
ALTER TABLE `settings_social_media`
  ADD PRIMARY KEY (`socialID`);

--
-- Indizes für die Tabelle `settings_static`
--
ALTER TABLE `settings_static`
  ADD PRIMARY KEY (`staticID`);

--
-- Indizes für die Tabelle `settings_themes`
--
ALTER TABLE `settings_themes`
  ADD PRIMARY KEY (`themeID`),
  ADD UNIQUE KEY `unique_modulname` (`modulname`),
  ADD UNIQUE KEY `uniq_theme_slug` (`slug`);

--
-- Indizes für die Tabelle `settings_themes_installed`
--
ALTER TABLE `settings_themes_installed`
  ADD PRIMARY KEY (`themeID`),
  ADD UNIQUE KEY `unique_name` (`name`);

--
-- Indizes für die Tabelle `settings_theme_options`
--
ALTER TABLE `settings_theme_options`
  ADD PRIMARY KEY (`optionID`),
  ADD UNIQUE KEY `uniq_theme_option` (`theme_slug`,`option_key`);

--
-- Indizes für die Tabelle `settings_widgets`
--
ALTER TABLE `settings_widgets`
  ADD PRIMARY KEY (`widget_key`);

--
-- Indizes für die Tabelle `settings_widgets_positions`
--
ALTER TABLE `settings_widgets_positions`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `system_update_history`
--
ALTER TABLE `system_update_history`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_update` (`version`,`channel`,`build`),
  ADD KEY `idx_installed_at` (`installed_at`);

--
-- Indizes für die Tabelle `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`userID`),
  ADD KEY `idx_last_update` (`last_update`);

--
-- Indizes für die Tabelle `user_profiles`
--
ALTER TABLE `user_profiles`
  ADD PRIMARY KEY (`userID`);

--
-- Indizes für die Tabelle `user_register_attempts`
--
ALTER TABLE `user_register_attempts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ip_address` (`ip_address`),
  ADD KEY `attempt_time` (`attempt_time`),
  ADD KEY `username` (`username`),
  ADD KEY `email` (`email`);

--
-- Indizes für die Tabelle `user_roles`
--
ALTER TABLE `user_roles`
  ADD PRIMARY KEY (`roleID`),
  ADD UNIQUE KEY `unique_role_name` (`role_name`),
  ADD UNIQUE KEY `unique_modulname` (`modulname`);

--
-- Indizes für die Tabelle `user_role_admin_navi_rights`
--
ALTER TABLE `user_role_admin_navi_rights`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_access` (`roleID`,`type`,`modulname`);

--
-- Indizes für die Tabelle `user_role_assignments`
--
ALTER TABLE `user_role_assignments`
  ADD PRIMARY KEY (`assignmentID`),
  ADD KEY `roleID` (`roleID`),
  ADD KEY `user_role_assignments` (`userID`) USING BTREE;

--
-- Indizes für die Tabelle `user_sessions`
--
ALTER TABLE `user_sessions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_session` (`session_id`),
  ADD KEY `userID` (`userID`);

--
-- Indizes für die Tabelle `user_settings`
--
ALTER TABLE `user_settings`
  ADD PRIMARY KEY (`userID`);

--
-- Indizes für die Tabelle `user_socials`
--
ALTER TABLE `user_socials`
  ADD PRIMARY KEY (`userID`);

--
-- Indizes für die Tabelle `user_stats`
--
ALTER TABLE `user_stats`
  ADD PRIMARY KEY (`userID`);

--
-- Indizes für die Tabelle `user_username`
--
ALTER TABLE `user_username`
  ADD PRIMARY KEY (`userID`);

--
-- Indizes für die Tabelle `visitors_live`
--
ALTER TABLE `visitors_live`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_user` (`userID`);

--
-- Indizes für die Tabelle `visitors_live_history`
--
ALTER TABLE `visitors_live_history`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `visitor_daily_counter`
--
ALTER TABLE `visitor_daily_counter`
  ADD PRIMARY KEY (`date`);

--
-- Indizes für die Tabelle `visitor_daily_counter_hits`
--
ALTER TABLE `visitor_daily_counter_hits`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_user_date` (`user_id`,`date`),
  ADD UNIQUE KEY `uq_iphash_date` (`ip_hash`,`date`),
  ADD KEY `idx_date` (`date`);

--
-- Indizes für die Tabelle `visitor_daily_iplist`
--
ALTER TABLE `visitor_daily_iplist`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_ip_date` (`ip`,`dates`),
  ADD KEY `idx_date` (`dates`);

--
-- Indizes für die Tabelle `visitor_daily_stats`
--
ALTER TABLE `visitor_daily_stats`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_date` (`date`);

--
-- Indizes für die Tabelle `visitor_statistics`
--
ALTER TABLE `visitor_statistics`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `backups`
--
ALTER TABLE `backups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `banned_ips`
--
ALTER TABLE `banned_ips`
  MODIFY `banID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `comments`
--
ALTER TABLE `comments`
  MODIFY `commentID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `contact`
--
ALTER TABLE `contact`
  MODIFY `contactID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT für Tabelle `failed_login_attempts`
--
ALTER TABLE `failed_login_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT für Tabelle `link_clicks`
--
ALTER TABLE `link_clicks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT für Tabelle `navigation_dashboard_categories`
--
ALTER TABLE `navigation_dashboard_categories`
  MODIFY `catID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT für Tabelle `navigation_dashboard_lang`
--
ALTER TABLE `navigation_dashboard_lang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=628;

--
-- AUTO_INCREMENT für Tabelle `navigation_dashboard_links`
--
ALTER TABLE `navigation_dashboard_links`
  MODIFY `linkID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=79;

--
-- AUTO_INCREMENT für Tabelle `navigation_website_lang`
--
ALTER TABLE `navigation_website_lang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=283;

--
-- AUTO_INCREMENT für Tabelle `navigation_website_main`
--
ALTER TABLE `navigation_website_main`
  MODIFY `mnavID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT für Tabelle `navigation_website_sub`
--
ALTER TABLE `navigation_website_sub`
  MODIFY `snavID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- AUTO_INCREMENT für Tabelle `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT für Tabelle `password_reset_attempts`
--
ALTER TABLE `password_reset_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT für Tabelle `plugins_about`
--
ALTER TABLE `plugins_about`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT für Tabelle `plugins_about_legacy`
--
ALTER TABLE `plugins_about_legacy`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT für Tabelle `plugins_achievements`
--
ALTER TABLE `plugins_achievements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT für Tabelle `plugins_achievements_admin_log`
--
ALTER TABLE `plugins_achievements_admin_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT für Tabelle `plugins_achievements_categories`
--
ALTER TABLE `plugins_achievements_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT für Tabelle `plugins_articles`
--
ALTER TABLE `plugins_articles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT für Tabelle `plugins_articles_categories`
--
ALTER TABLE `plugins_articles_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT für Tabelle `plugins_articles_comments`
--
ALTER TABLE `plugins_articles_comments`
  MODIFY `commentID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `plugins_articles_settings`
--
ALTER TABLE `plugins_articles_settings`
  MODIFY `articlessetID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT für Tabelle `plugins_carousel`
--
ALTER TABLE `plugins_carousel`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT für Tabelle `plugins_carousel_lang`
--
ALTER TABLE `plugins_carousel_lang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT für Tabelle `plugins_carousel_settings`
--
ALTER TABLE `plugins_carousel_settings`
  MODIFY `carouselID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT für Tabelle `plugins_downloads`
--
ALTER TABLE `plugins_downloads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT für Tabelle `plugins_downloads_categories`
--
ALTER TABLE `plugins_downloads_categories`
  MODIFY `categoryID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT für Tabelle `plugins_downloads_logs`
--
ALTER TABLE `plugins_downloads_logs`
  MODIFY `logID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `plugins_footer`
--
ALTER TABLE `plugins_footer`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT für Tabelle `plugins_footer_lang`
--
ALTER TABLE `plugins_footer_lang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=99;

--
-- AUTO_INCREMENT für Tabelle `plugins_forum_boards`
--
ALTER TABLE `plugins_forum_boards`
  MODIFY `boardID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT für Tabelle `plugins_forum_categories`
--
ALTER TABLE `plugins_forum_categories`
  MODIFY `catID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT für Tabelle `plugins_forum_permissions_board`
--
ALTER TABLE `plugins_forum_permissions_board`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT für Tabelle `plugins_forum_permissions_categories`
--
ALTER TABLE `plugins_forum_permissions_categories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT für Tabelle `plugins_forum_permissions_threads`
--
ALTER TABLE `plugins_forum_permissions_threads`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `plugins_forum_posts`
--
ALTER TABLE `plugins_forum_posts`
  MODIFY `postID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT für Tabelle `plugins_forum_threads`
--
ALTER TABLE `plugins_forum_threads`
  MODIFY `threadID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT für Tabelle `plugins_forum_uploaded_images`
--
ALTER TABLE `plugins_forum_uploaded_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `plugins_gallery`
--
ALTER TABLE `plugins_gallery`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT für Tabelle `plugins_gallery_categories`
--
ALTER TABLE `plugins_gallery_categories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT für Tabelle `plugins_gametracker_servers`
--
ALTER TABLE `plugins_gametracker_servers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT für Tabelle `plugins_joinus_applications`
--
ALTER TABLE `plugins_joinus_applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT für Tabelle `plugins_joinus_roles`
--
ALTER TABLE `plugins_joinus_roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT für Tabelle `plugins_joinus_squads`
--
ALTER TABLE `plugins_joinus_squads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT für Tabelle `plugins_joinus_types`
--
ALTER TABLE `plugins_joinus_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT für Tabelle `plugins_links`
--
ALTER TABLE `plugins_links`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT für Tabelle `plugins_links_categories`
--
ALTER TABLE `plugins_links_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT für Tabelle `plugins_links_settings`
--
ALTER TABLE `plugins_links_settings`
  MODIFY `linkssetID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT für Tabelle `plugins_messages`
--
ALTER TABLE `plugins_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT für Tabelle `plugins_news`
--
ALTER TABLE `plugins_news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT für Tabelle `plugins_news_categories`
--
ALTER TABLE `plugins_news_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT für Tabelle `plugins_news_lang`
--
ALTER TABLE `plugins_news_lang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT für Tabelle `plugins_partners`
--
ALTER TABLE `plugins_partners`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT für Tabelle `plugins_partners_settings`
--
ALTER TABLE `plugins_partners_settings`
  MODIFY `partnerssetID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT für Tabelle `plugins_pricing_features`
--
ALTER TABLE `plugins_pricing_features`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT für Tabelle `plugins_pricing_plans`
--
ALTER TABLE `plugins_pricing_plans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT für Tabelle `plugins_raidplaner_attendance`
--
ALTER TABLE `plugins_raidplaner_attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `plugins_raidplaner_bis_list`
--
ALTER TABLE `plugins_raidplaner_bis_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `plugins_raidplaner_bosses`
--
ALTER TABLE `plugins_raidplaner_bosses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT für Tabelle `plugins_raidplaner_characters`
--
ALTER TABLE `plugins_raidplaner_characters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `plugins_raidplaner_character_gear`
--
ALTER TABLE `plugins_raidplaner_character_gear`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `plugins_raidplaner_classes`
--
ALTER TABLE `plugins_raidplaner_classes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT für Tabelle `plugins_raidplaner_events`
--
ALTER TABLE `plugins_raidplaner_events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT für Tabelle `plugins_raidplaner_event_setup`
--
ALTER TABLE `plugins_raidplaner_event_setup`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT für Tabelle `plugins_raidplaner_items`
--
ALTER TABLE `plugins_raidplaner_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT für Tabelle `plugins_raidplaner_loot_distributed`
--
ALTER TABLE `plugins_raidplaner_loot_distributed`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `plugins_raidplaner_loot_history`
--
ALTER TABLE `plugins_raidplaner_loot_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `plugins_raidplaner_participants`
--
ALTER TABLE `plugins_raidplaner_participants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `plugins_raidplaner_roles`
--
ALTER TABLE `plugins_raidplaner_roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT für Tabelle `plugins_raidplaner_setup`
--
ALTER TABLE `plugins_raidplaner_setup`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT für Tabelle `plugins_raidplaner_signups`
--
ALTER TABLE `plugins_raidplaner_signups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `plugins_raidplaner_templates`
--
ALTER TABLE `plugins_raidplaner_templates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT für Tabelle `plugins_raidplaner_wishlists`
--
ALTER TABLE `plugins_raidplaner_wishlists`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `plugins_rules`
--
ALTER TABLE `plugins_rules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT für Tabelle `plugins_rules_settings`
--
ALTER TABLE `plugins_rules_settings`
  MODIFY `rulessetID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT für Tabelle `plugins_shoutbox_messages`
--
ALTER TABLE `plugins_shoutbox_messages`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT für Tabelle `plugins_sponsors`
--
ALTER TABLE `plugins_sponsors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT für Tabelle `plugins_sponsors_settings`
--
ALTER TABLE `plugins_sponsors_settings`
  MODIFY `sponsorssetID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT für Tabelle `plugins_teamspeak`
--
ALTER TABLE `plugins_teamspeak`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT für Tabelle `plugins_todo`
--
ALTER TABLE `plugins_todo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT für Tabelle `plugins_twitch_settings`
--
ALTER TABLE `plugins_twitch_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT für Tabelle `plugins_userlist_settings`
--
ALTER TABLE `plugins_userlist_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT für Tabelle `plugins_youtube`
--
ALTER TABLE `plugins_youtube`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT für Tabelle `plugins_youtube_settings`
--
ALTER TABLE `plugins_youtube_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT für Tabelle `ratings`
--
ALTER TABLE `ratings`
  MODIFY `ratingID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `settings`
--
ALTER TABLE `settings`
  MODIFY `settingID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT für Tabelle `settings_content_lang`
--
ALTER TABLE `settings_content_lang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT für Tabelle `settings_headstyle_config`
--
ALTER TABLE `settings_headstyle_config`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT für Tabelle `settings_imprint`
--
ALTER TABLE `settings_imprint`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT für Tabelle `settings_languages`
--
ALTER TABLE `settings_languages`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT für Tabelle `settings_plugins`
--
ALTER TABLE `settings_plugins`
  MODIFY `pluginID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=90;

--
-- AUTO_INCREMENT für Tabelle `settings_plugins_installed`
--
ALTER TABLE `settings_plugins_installed`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=92;

--
-- AUTO_INCREMENT für Tabelle `settings_plugins_lang`
--
ALTER TABLE `settings_plugins_lang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=588;

--
-- AUTO_INCREMENT für Tabelle `settings_seo_meta_lang`
--
ALTER TABLE `settings_seo_meta_lang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=193;

--
-- AUTO_INCREMENT für Tabelle `settings_site_lock`
--
ALTER TABLE `settings_site_lock`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `settings_social_media`
--
ALTER TABLE `settings_social_media`
  MODIFY `socialID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT für Tabelle `settings_static`
--
ALTER TABLE `settings_static`
  MODIFY `staticID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `settings_themes`
--
ALTER TABLE `settings_themes`
  MODIFY `themeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT für Tabelle `settings_themes_installed`
--
ALTER TABLE `settings_themes_installed`
  MODIFY `themeID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT für Tabelle `settings_theme_options`
--
ALTER TABLE `settings_theme_options`
  MODIFY `optionID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=465;

--
-- AUTO_INCREMENT für Tabelle `settings_widgets_positions`
--
ALTER TABLE `settings_widgets_positions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=816;

--
-- AUTO_INCREMENT für Tabelle `system_update_history`
--
ALTER TABLE `system_update_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT für Tabelle `users`
--
ALTER TABLE `users`
  MODIFY `userID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT für Tabelle `user_register_attempts`
--
ALTER TABLE `user_register_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT für Tabelle `user_roles`
--
ALTER TABLE `user_roles`
  MODIFY `roleID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT für Tabelle `user_role_admin_navi_rights`
--
ALTER TABLE `user_role_admin_navi_rights`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=132;

--
-- AUTO_INCREMENT für Tabelle `user_role_assignments`
--
ALTER TABLE `user_role_assignments`
  MODIFY `assignmentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT für Tabelle `user_sessions`
--
ALTER TABLE `user_sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT für Tabelle `user_username`
--
ALTER TABLE `user_username`
  MODIFY `userID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT für Tabelle `visitors_live`
--
ALTER TABLE `visitors_live`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37768;

--
-- AUTO_INCREMENT für Tabelle `visitors_live_history`
--
ALTER TABLE `visitors_live_history`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3038;

--
-- AUTO_INCREMENT für Tabelle `visitor_daily_counter_hits`
--
ALTER TABLE `visitor_daily_counter_hits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=137;

--
-- AUTO_INCREMENT für Tabelle `visitor_daily_iplist`
--
ALTER TABLE `visitor_daily_iplist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=135;

--
-- AUTO_INCREMENT für Tabelle `visitor_daily_stats`
--
ALTER TABLE `visitor_daily_stats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT für Tabelle `visitor_statistics`
--
ALTER TABLE `visitor_statistics`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=141;

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `plugins_downloads`
--
ALTER TABLE `plugins_downloads`
  ADD CONSTRAINT `plugins_downloads_ibfk_1` FOREIGN KEY (`categoryID`) REFERENCES `plugins_downloads_categories` (`categoryID`) ON DELETE CASCADE;

--
-- Constraints der Tabelle `plugins_downloads_logs`
--
ALTER TABLE `plugins_downloads_logs`
  ADD CONSTRAINT `plugins_downloads_logs_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`),
  ADD CONSTRAINT `plugins_downloads_logs_ibfk_2` FOREIGN KEY (`fileID`) REFERENCES `plugins_downloads` (`id`) ON DELETE CASCADE;

--
-- Constraints der Tabelle `plugins_forum_permissions_board`
--
ALTER TABLE `plugins_forum_permissions_board`
  ADD CONSTRAINT `fk_acl_board` FOREIGN KEY (`boardID`) REFERENCES `plugins_forum_boards` (`boardID`) ON DELETE CASCADE;

--
-- Constraints der Tabelle `plugins_forum_permissions_categories`
--
ALTER TABLE `plugins_forum_permissions_categories`
  ADD CONSTRAINT `fk_acl_category` FOREIGN KEY (`catID`) REFERENCES `plugins_forum_categories_backup` (`catID`) ON DELETE CASCADE;

--
-- Constraints der Tabelle `plugins_forum_permissions_threads`
--
ALTER TABLE `plugins_forum_permissions_threads`
  ADD CONSTRAINT `fk_acl_thread` FOREIGN KEY (`threadID`) REFERENCES `plugins_forum_threads_backup` (`threadID`) ON DELETE CASCADE;

--
-- Constraints der Tabelle `plugins_pricing_features`
--
ALTER TABLE `plugins_pricing_features`
  ADD CONSTRAINT `plugins_pricing_features_ibfk_1` FOREIGN KEY (`plan_id`) REFERENCES `plugins_pricing_plans` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
