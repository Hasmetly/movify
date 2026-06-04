-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Anamakine: 127.0.0.1
-- Üretim Zamanı: 05 Haz 2026, 01:02:27
-- Sunucu sürümü: 10.4.32-MariaDB
-- PHP Sürümü: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `movify`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `created_at`) VALUES
(1, 'Tüm Kategoriler', 'tum-kategoriler', '2026-05-22 00:32:16'),
(2, 'Aksiyon', 'aksiyon', '2026-05-22 00:32:16'),
(3, 'Anime', 'anime', '2026-05-22 00:32:16'),
(4, 'Astroloji', 'astroloji', '2026-05-22 00:32:16'),
(5, 'Bağımsız', 'bagimsiz', '2026-05-22 00:32:16'),
(6, 'Belgeseller', 'belgeseller', '2026-05-22 00:32:16'),
(7, 'Bilim Kurgu', 'bilim-kurgu', '2026-05-22 00:32:16'),
(8, 'Çocuk ve Aile', 'cocuk-ve-aile', '2026-05-22 00:32:16'),
(9, 'Drama', 'drama', '2026-05-22 00:32:16'),
(10, 'Fantastik', 'fantastik', '2026-05-22 00:32:16'),
(11, 'Gerilim', 'gerilim', '2026-05-22 00:32:16'),
(12, 'Kısa Filmler', 'kisa-filmler', '2026-05-22 00:32:16'),
(13, 'Klasikler', 'klasikler', '2026-05-22 00:32:16'),
(14, 'Hollywood', 'hollywood', '2026-05-22 00:32:16'),
(15, 'Korku', 'korku', '2026-05-22 00:32:16'),
(16, 'Komedi', 'komedi', '2026-05-22 00:32:16'),
(17, 'Ödüllü Yapımlar', 'odullu-yapimlar', '2026-05-22 00:32:16'),
(18, 'Romantizm', 'romantizm', '2026-05-22 00:32:16'),
(19, 'Spor', 'spor', '2026-05-22 00:32:16'),
(20, 'Stand-Up', 'stand-up', '2026-05-22 00:32:16'),
(21, 'Yerli Yapımlar', 'yerli-yapimlar', '2026-05-22 00:32:16'),
(22, 'Suç', 'suc', '2026-05-22 00:32:16'),
(23, 'Amerikan Dizileri', 'amerikan-dizileri', '2026-05-22 01:42:18'),
(24, 'Gişe Rekortmenleri', 'gise-rekortmenleri', '2026-05-22 01:42:18'),
(25, 'Senin için seçtiklerimiz', 'senin-icin-sectiklerimiz', '2026-05-22 02:29:40'),
(26, 'Eleştirmenlerden Tam Not Alanlar', 'elestirmenlerden-tam-not-alanlar', '2026-05-22 02:29:40'),
(27, 'Sadece Movify\'da', 'sadece-movifyda', '2026-05-22 02:29:40'),
(28, 'Animeler', 'animeler', '2026-05-22 02:29:40'),
(29, 'Holywood Yapımları', 'holywood-yapimlari', '2026-05-22 02:29:40'),
(30, 'Amerikan Filmleri', 'amerikan-filmleri', '2026-06-05 01:09:18');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `profile_id` int(11) NOT NULL,
  `content_id` int(11) NOT NULL,
  `comment_text` text NOT NULL,
  `is_like` tinyint(1) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `content`
--

CREATE TABLE `content` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `type` enum('movie','series') NOT NULL DEFAULT 'movie',
  `director` varchar(255) DEFAULT NULL,
  `original_language` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `cover_image` varchar(255) DEFAULT NULL,
  `banner_image` varchar(255) DEFAULT NULL,
  `release_year` int(4) DEFAULT NULL,
  `age_rating` varchar(10) DEFAULT NULL,
  `imdb_rating` decimal(3,1) DEFAULT NULL,
  `rotten_tomatoes_rating` int(3) DEFAULT NULL,
  `duration_minutes` int(5) DEFAULT NULL,
  `views_count` int(11) NOT NULL DEFAULT 0,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `is_coming_soon` tinyint(1) NOT NULL DEFAULT 0,
  `is_exclusive` tinyint(1) NOT NULL DEFAULT 0,
  `is_top10` tinyint(1) NOT NULL DEFAULT 0,
  `coming_soon_date` date DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `content`
--

INSERT INTO `content` (`id`, `title`, `type`, `director`, `original_language`, `description`, `cover_image`, `banner_image`, `release_year`, `age_rating`, `imdb_rating`, `rotten_tomatoes_rating`, `duration_minutes`, `views_count`, `is_featured`, `is_coming_soon`, `is_exclusive`, `is_top10`, `coming_soon_date`, `created_at`, `updated_at`) VALUES
(4, 'Prens', 'series', NULL, NULL, '', 'cover_1779402331_833.jpg', 'banner_1779402331_340.jpg', 2026, '13+', 0.0, NULL, 0, 0, 0, 0, 1, 0, NULL, '2026-05-22 01:25:31', '2026-05-22 02:21:50'),
(6, 'Muhteşem Yüzyıl', 'series', 'Timur Savcı', 'tr', 'Osmanlı İmparatorluğu\'nun XV. padişahı I. Süleyman, yirmi altı yaşında bu büyük iktidarı yönetmeye başladığında,  önüne öyle bir hedef koymuştu ki;  Büyük İskender’den daha güçlü ve yaygın bir dünyada egemenlik kuracak ve Osmanlı’yı yenilmez kılacaktı. Bu yürüyüş ona; batıdaki savaş zaferleri nedeniyle Muhteşem Süleyman, doğuda ise adaletli yönetimine atfen Kanunî Sultan Süleyman ismini kazandıracaktı.', 'cover_1779918833_820.jpg', 'banner_1779918834_487.jpg', 2011, '13+', 8.1, NULL, 90, 0, 1, 0, 0, 0, NULL, '2026-05-22 03:56:53', '2026-05-28 00:54:16'),
(7, 'Arka Sokaklar', 'series', '', 'tr', 'İstanbul Emniyet Müdürlüğü Asayiş Şube Müdürlüğü\'nde özel bir ekipte görev yapan polislerin aile yaşamları ve İstanbul sokaklarındaki maceraları anlatılmaktadır. Ve bu şehrin her sokağını, herkes için daha güzel, daha yaşanır bir yer yapmak uğruna her türlü kötülüğe ve sıkıntıya meydan okuyan, sevinçte, kederde, aşkta ve yalnızlıkta her zaman birbirlerinin yanında olan bu yürekli polisler, “birliktelik ruhu” ve “mücadele azmiyle” minibüsleriyle bu metropolün sokaklarını arşınlamakta ve karşılarına çıkan her türlü kanunsuzlukla savaşarak suçluların korkulu rüyası olmaktadırlar. Ekip görevleri sırasında değişik ve çeşitli insan hikayeleriyle sürekli karşılaşmaktadır. Zaman zaman gülümseten, zaman zaman da iç burkan bu hikayelere, meslek yıllarının tecrübesi ve babalığıyla yaklaşan, ekibin diğer genç üyelerine de yol gösteren Emniyet amiri Rıza Baba olur.', 'cover_1779918777_984.jpg', 'banner_1779918778_617.jpg', 2006, '16', 5.6, NULL, 0, 0, 1, 0, 0, 0, NULL, '2026-05-27 23:56:34', '2026-05-28 00:52:59'),
(10, 'Kurtlar Vadisi', 'series', 'Osman Sınav, Raci Şaşmaz', 'tr', 'Sokaklardan başlayıp belli bir hiyerarşik bütünlük içinde kademe kademe yükselen ve en tepede son derece etkili bir \"konseye\" dönüşerek ülkeyi avcuna almayı amaçlayan çok güçlü bir mafya örgütü ile o örgütle mücadeleye soyunanların serüvenleri anlatılır. Bu hem toplumsal hem de bireysel yansımaları olan bir serüvendir.', 'cover_1779917680_212.jpg', 'banner_1779917682_851.jpg', 2003, '13+', 7.2, NULL, 90, 0, 1, 0, 0, 0, NULL, '2026-05-28 00:34:49', '2026-05-28 00:34:49'),
(12, 'Kolpaçino', 'movie', 'Atıl İnaç', 'tr', 'Birbirine kenetlenmiş yedi kişinin bir günde başına gelen maceraların öyküsü... \"Para darphanede basılır, imzası atılır, banknot yapılır, deste deste akar gider. Milyonlarca temiz, milyonlarca pis el değer. Anlatılmaz bir sevgisi, anlatılmaz bir acısı vardır. Dünyada para için istenilecek en güzel dilek pis tarafını tutmamaktır, eğer tutarsan.', 'cover_1779920489_545.jpg', 'banner_1779920496_167.jpg', 2009, '13+', 5.8, NULL, 97, 0, 1, 0, 0, 0, NULL, '2026-05-28 01:21:37', '2026-05-28 01:21:37'),
(13, 'Eşref Rüya', 'series', 'Ethem Özışık', 'tr', 'Eşref yıllardır her yerde aradığı Rüya’sının tam karşısında olduğunu bilmeden yine onun için kendini ateşe atar.', 'cover_1780607636_943.jpg', 'banner_1780607644_370.jpg', 2025, '13+', 7.1, NULL, 130, 0, 1, 0, 0, 0, NULL, '2026-06-05 00:14:07', '2026-06-05 00:14:07'),
(14, 'Yeraltı', 'series', 'Berna Aruz', 'tr', 'Ailesinin katilini öldürerek intikamını alan Haydar Ali, cezaevine düştüğünde kendini Türkiye’nin en tehlikeli yeraltı kartellerinden birinin ortasında bulur. Ancak asıl ölümcül yüzleşme üç yıl sonra özgürlüğüne kavuştuğunda başlar: Haydar Ali’nin hâlâ unutamadığı kadın, artık onun en yakınındaki adamın karısıdır.', 'cover_1780608386_992.jpg', 'banner_1780608408_375.jpg', 2026, '13+', 8.0, NULL, 0, 0, 1, 0, 0, 0, NULL, '2026-06-05 00:26:49', '2026-06-05 00:26:49'),
(15, 'Suikastçı', 'movie', 'Philip G. Atwell', 'en', 'FBI ajanı Jack Crawford, ortağının Asya yeraltı dünyasının en vahşi suikastçilerinden biri tarafından öldürülmesinin intikamını almaya yemin eder. Fakat karşısındaki adam hiç de yabana atılacak biri değildir. Jack\'in tüm çabalarına rağmen kötü şöhretli Rogue, izini kaybettirir ve üç yıl boyunca ortadan kaybolur.Üç yıl sonra ise çok daha büyük bir kapışmanın arifesinde yeniden ortaya çıkacaktır. Çin mafya lideri Chang ve Japon Yakuza patronu Shiro arasında kanlı bir savaş başlamak üzere iken tam bu çatışmanın ortasında Rogue\'un kaybolan izi yeniden ortaya çıkar. İntikam yeminini üç yıl boyunca canlı tutmuş olan Jack ise bu fırsatı kaçırmayacaktır.Bir adamın en iyi arkadaşının intikamını almak için en fazla nereye kadar gidebileceğini irdeleyen gerilimli bir dövüş filmi Suikastçi. Kamera önündeyse dövüş filmlerinin usta isimlerinden Jet Li ve aksiyon filmlerinin aranan isimlerinden Jason Statham var.', 'cover_1780608978_256.jpg', 'banner_1780608980_553.jpg', 2007, '13+', 6.4, NULL, 103, 0, 1, 0, 0, 0, NULL, '2026-06-05 00:36:22', '2026-06-05 00:40:18'),
(16, 'Kod Adı Cain', 'movie', 'Віталій Васильков', 'ru', 'Sarah Ogden, hayatı boyunca gazeteci kimliğiyle bir kişiyi aramaya and içmiştir. Ölümcül tehlikeler atlatmasına karşın yılmadan dünyanın her yerini gezip, aradığı kişiyi en sonunda Belarus’ta bulacaktır. Bir uçurumun kenarında bulduğu bu kişiye tek yapmak istediği şey onu yok etmek olacaktır.', 'cover_1780610390_461.jpg', NULL, 2015, '13+', 2.0, NULL, 102, 0, 0, 0, 0, 0, NULL, '2026-06-05 00:59:53', '2026-06-05 00:59:53'),
(17, '12 Savaşçı', 'movie', 'Nicolai Fuglsig', 'en', 'Ailelerini geride bırakan ekip, Kuzey Afganistan\'ın uzak, engebeli arazisine yerleştirilir ve burada ortak düşmanları Taliban ve El-Kaide\'ye karşı savaşmak için General Rashid Dostum\'u ikna etmeleri gerekmektedir. Karşılıklı güvensizliği ve geniş bir kültürel bölünmeyi yenmenin yanı sıra Amerikalılar, Afgan atlı askerlerinin taktiklerini de benimsemek zorunda kalırlar. Kolay olmayan bir bağ oluşturup saygı oluşturmalarına rağmen yeni müttefikler çok fazla sayıda ve tutsak almayan acımasız bir düşmanla karşı karşıya kalırlar.', 'cover_1780611085_986.jpg', 'banner_1780611086_129.jpg', 2018, '13+', 6.4, NULL, 130, 0, 0, 0, 0, 0, NULL, '2026-06-05 01:11:47', '2026-06-05 01:11:47'),
(18, 'Amerika Batıyor', 'movie', 'Mario N. Bonassin', 'en', 'Erimekte olan buzulların getirdiği aşırı sel, hızlı erozyon ise fay hatlarını gevşeterek depremlere ve dev tsunamilere neden olmaktadır. Bilim insanları, Kuzey Amerika batarken su dengesini sağlamak için zamanla yarışıyor.', 'cover_1780611272_510.jpg', 'banner_1780611276_308.jpg', 2023, '13+', 4.0, NULL, 83, 0, 0, 0, 0, 0, NULL, '2026-06-05 01:14:36', '2026-06-05 01:14:36'),
(19, 'Amerikan Menekşesi', 'movie', 'Tim Disney', 'en', 'Teksas’ta dört küçük çocuğuyla birlikte yaşayan Dee Roberts bir restoranda garsonluk yapmaktadır. 24 Yaşındaki siyahi anne narkotik polisinin yaptığı bir operasyon sırasında iftiraya uğratılarak tutuklanır. Okul bölgesinde uyuşturucu satmakla suçlanan genç kadın, mahkemenin kendisine verdiği avukatın  itiraf anlaşmasını ret ederek masumiyetini kanıtlamak için büyük bir mücadele vermeye başlar. 2000 yılında yaşanmış gerçek bir olaydan uyarlanan bu etkileyici film, Amerikan toplumunda ırkçılığın halen sürdüğünü gösteren trajedik hakikatleri anlatıyor…', 'cover_1780611485_876.jpg', 'banner_1780611487_951.jpg', 2008, '13+', 6.7, NULL, 103, 0, 1, 0, 0, 0, NULL, '2026-06-05 01:18:08', '2026-06-05 01:18:08'),
(20, 'The Commuter', 'movie', 'Jaume Collet-Serra', 'en', 'Sigorta pazarlamacısı Michael’ın hayatı işten eve gidip geldiği bir rutinle devam etmektedir. Fakat gizemli bir yabancıyla iletişime geçerek bulunduğu trendeki bir kişinin kimliğini ortaya çıkarmak zorunda kalır. Zamana karşı yarışan Michael, kendini ölümcül bir komplonun içinde bulur. Hayatta kalmak ve diğer yolcuları kurtarmak artık onun elindedir.', 'cover_1780611746_268.jpg', 'banner_1780611746_804.jpg', 2018, '13+', 6.4, NULL, 104, 0, 0, 0, 0, 0, NULL, '2026-06-05 01:22:26', '2026-06-05 01:22:26'),
(21, 'Transporter 3', 'movie', 'Olivier Megaton', 'en', 'Frank, taşıyıcılık görevini artık bırakmış olup ona yeni bir iş için teklifte bulunanları da başka birisine yönlendirmiştir. Fakat tavsiyede bulunduğu kişi, görevi başaramayınca iş yine Frank\'e kalır. Onu, bu görevi yapması için zorlarlar. Frank, Marsilya\'daki Ukrayna Çevre Koruma Ajansı Başkanı Leonid\'in kaçırılan kızı Valentina\'yı Karadeniz kıyısındaki Odessa\'ya götürmekle yükümlüdür. Frank, yolculuk sırasında bir yandan bu işi alması için baskı yapan insanlarla uğraşırken bir yandan da Vasilev tarafından gönderilen ajanlarla başa çıkmak zorundadır.', 'cover_1780611934_620.jpg', 'banner_1780611935_103.jpg', 2008, '13+', 6.2, NULL, 104, 0, 0, 0, 0, 0, NULL, '2026-06-05 01:25:36', '2026-06-05 01:25:36'),
(22, 'Tarihin Ölümsüz Savaşçıları', 'series', '', 'tr', 'Tarihte iz bırakan, unutulmaz komutanların hayat hikayeleri...', 'cover_1780612827_384.jpg', NULL, 2022, '13+', 0.0, NULL, 30, 0, 0, 0, 0, 0, NULL, '2026-06-05 01:40:29', '2026-06-05 01:40:29'),
(23, 'Vahşi Pasifik', 'movie', '', 'tr', '', 'cover_1780613215_462.jpg', NULL, 2016, '13+', 7.0, NULL, 91, 0, 0, 0, 0, 0, NULL, '2026-06-05 01:46:55', '2026-06-05 01:46:55'),
(24, 'Orange', 'series', '', 'ja', '', 'cover_1780613353_797.jpg', 'banner_1780613355_585.jpg', 2016, '13+', 8.0, NULL, 24, 0, 0, 0, 0, 0, NULL, '2026-06-05 01:49:16', '2026-06-05 01:49:16'),
(26, 'Attack On Titan', 'series', '', 'ja', 'Birkaç yüzyıl önce insanlar, devler tarafından neredeyse tamamen yok edilmiştir. Bu devler uzun boyludur, zeki değildirler ve insanları yiyerek beslenirler. Küçük bir grup insan, çareyi en büyük devlerden bile uzun olan yüksek duvarlarla çevrili bir şehre kendilerini kapatarak bulmuşlardır. Günümüze gelindiğinde şehirde 100 yıldır hiç dev görülmemiştir. Genç çocuk Eren ve üvey kız kardeşi Mikasa, büyük bir dev tarafından şehrin duvarlarının yıkılışına tanık olurlar. Devler şehre akın etmeye başlar ve iki çocuk da annelerinin canlı canlı yenişine korku içinde tanıklık ederler. Eren her bir devi öldürüp tüm insanlığın intikamını alacağına yemin eder.', 'cover_1780613551_740.jpg', 'banner_1780613552_388.jpg', 2013, '13+', 8.7, NULL, 0, 0, 0, 0, 0, 0, NULL, '2026-06-05 01:52:32', '2026-06-05 01:52:32');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `content_cast`
--

CREATE TABLE `content_cast` (
  `id` int(11) NOT NULL,
  `content_id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `role` enum('actor','director') NOT NULL DEFAULT 'actor',
  `character_name` varchar(150) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `sort_order` int(3) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `content_cast`
--

INSERT INTO `content_cast` (`id`, `content_id`, `name`, `role`, `character_name`, `photo`, `sort_order`) VALUES
(1, 10, 'Necati Şaşmaz', 'actor', 'Polat Alemdar', 'https://image.tmdb.org/t/p/w185/y0xyyuveJ8XHoW37asEPS4daV8R.jpg', 1),
(2, 10, 'Özgü Namal', 'actor', 'Elif Eylül', 'https://image.tmdb.org/t/p/w185/i2qUbe9wYZJFgY79KrfhRVnscMz.jpg', 2),
(3, 10, 'Kenan Çoban', 'actor', 'Abdülhey Çoban / Zülfü Yüksel', 'https://image.tmdb.org/t/p/w185/cTFFDozKPbSCPMGP4t9lPZEFswc.jpg', 3),
(4, 10, 'Gürkan Uygun', 'actor', 'Memati Baş', 'https://image.tmdb.org/t/p/w185/8FCX0ANplCW9N8ytnqWmZzFyLZj.jpg', 4),
(5, 10, 'İpek Tenolcay', 'actor', 'Nesrin Çakır', 'https://image.tmdb.org/t/p/w185/eoDIoI0uhNibVEH8jW0PLheH45o.jpg', 5),
(6, 10, 'Erhan Ufak', 'actor', 'Erhan Ufuk', 'https://image.tmdb.org/t/p/w185/h8aS7AfZHMkJXfTkEBmBwVNDFRL.jpg', 6),
(7, 10, 'Emin Olcay', 'actor', 'Ömer Candan', 'https://image.tmdb.org/t/p/w185/3UhFF3shFm7vDlp4h7sJA8VoY0T.jpg', 7),
(8, 10, 'Serpil Tamur', 'actor', 'Nazife Candan', 'https://image.tmdb.org/t/p/w185/8Kex5TFRx61joC3MOMjOz1RMyGd.jpg', 8),
(9, 10, 'Hande Kazanova', 'actor', 'Canan Çavan', 'https://image.tmdb.org/t/p/w185/gdcw6FlOz6eaRb0iat1VB5XubUE.jpg', 9),
(10, 10, 'Attila Olgaç', 'actor', 'Kılıç', 'https://image.tmdb.org/t/p/w185/1Ljk22vmXBgQK6j82Yl7H40RNQu.jpg', 10),
(11, 10, 'İstemi Betil', 'actor', 'Laz Ziya', 'https://image.tmdb.org/t/p/w185/1yfkfLrs6Tf58dbTxL7M47MTj0m.jpg', 11),
(12, 10, 'Adnan Biricik', 'actor', 'Nizamettin Güvenç', 'https://image.tmdb.org/t/p/w185/6UC33oTLSb2faTMkk0m8Q6Dj03L.jpg', 12),
(13, 7, 'Zafer Ergin', 'actor', 'Rıza Soylu', 'https://image.tmdb.org/t/p/w185/4xZZ3DmJ8yUnL64C1gHxJsDtRnC.jpg', 1),
(14, 7, 'Şevket Çoruh', 'actor', 'Mesut Güneri', 'https://image.tmdb.org/t/p/w185/ntFpsS7mt628MWFVBDTxzq3V28n.jpg', 2),
(15, 7, 'Özgür Ozan', 'actor', 'Hüsnü Çoban', 'https://image.tmdb.org/t/p/w185/6xLeuaFaKcuxBdZ6nMtlLutqxNF.jpg', 3),
(16, 7, 'Oya Okar', 'actor', 'Selin Demirci Güneri', 'https://image.tmdb.org/t/p/w185/2KhTTD1aCgGcaD53lvX77Ot5awr.jpg', 4),
(17, 7, 'Ozan Çobanoğlu', 'actor', 'Hakan Çınar', 'https://image.tmdb.org/t/p/w185/7enWJpXEYoZIR9Fu1PSQE2vkwP8.jpg', 5),
(18, 7, 'Alp Korkmaz', 'actor', 'Ali Akdoğan', 'https://image.tmdb.org/t/p/w185/yKWZcbnPFyB7ZxEsGBRH836kPII.jpg', 6),
(19, 7, 'Ebru Cündübeyoğlu', 'actor', 'Candan Ceylanlı', 'https://image.tmdb.org/t/p/w185/lhJ9LpOxevXVPhe0WxsEx0DcovL.jpg', 7),
(20, 7, 'Sarp Levendoğlu', 'actor', 'Barış Bozoğlu', 'https://image.tmdb.org/t/p/w185/8BwEWqLcyZxCB0kEpxUzwZl9x7W.jpg', 8),
(21, 7, 'Burak Satıbol', 'actor', 'Zeki Çevik', 'https://image.tmdb.org/t/p/w185/vLCySHBIBstc5PbpKC7W9u7THZq.jpg', 9),
(22, 7, 'Nazlı Tosunoğlu', 'actor', 'Nazike Özçaylan', 'https://image.tmdb.org/t/p/w185/fMp12NymioJSs4xVT4uCcjyeNrR.jpg', 10),
(23, 7, 'Hülya Kalebayır Çelik', 'actor', 'Ayla Soylu (2)', '', 11),
(24, 7, 'Gaye Gürsel', 'actor', 'Esra Çoban', 'https://image.tmdb.org/t/p/w185/fd9oG9jJJ8hcbgRWVFbdzQoHykO.jpg', 12),
(25, 6, 'Halit Ergenç', 'actor', 'I. Süleyman', 'https://image.tmdb.org/t/p/w185/7L5ivMz1S7ArHTvUaslVVV3ML42.jpg', 1),
(26, 6, 'Nur Fettahoğlu', 'actor', 'Mahidevran Sultan', 'https://image.tmdb.org/t/p/w185/yMxCbHQebr1ZVjOphUw3V1U5anC.jpg', 2),
(27, 6, 'Meryem Uzerli', 'actor', 'Hürrem Sultan', 'https://image.tmdb.org/t/p/w185/xWb9GuPDFmrLCpuocFs45O3rG0W.jpg', 3),
(28, 6, 'Engin Öztürk', 'actor', 'Şehzade Selim', 'https://image.tmdb.org/t/p/w185/nY0PvgJMtLXDlgrix4xwayqHu5C.jpg', 4),
(29, 6, 'Merve Boluğur', 'actor', 'Nurbanu Sultan', 'https://image.tmdb.org/t/p/w185/1JomPe1BBJ2FobpZ5756JkUKI1Q.jpg', 5),
(30, 6, 'Nebahat Çehre', 'actor', 'Valide Ayşe Hafsa Sultan', 'https://image.tmdb.org/t/p/w185/sWqAQdUAM5RJS0xdksHJQbnB5tb.jpg', 6),
(31, 6, 'Okan Yalabık', 'actor', 'Pargalı İbrahim Paşa', 'https://image.tmdb.org/t/p/w185/xWbbpbln5lL2nlaPKDzGrxWe10E.jpg', 7),
(32, 6, 'Selma Ergeç', 'actor', 'Hatice Sultan', 'https://image.tmdb.org/t/p/w185/8pNvPKs1WeUbPW4UbOr3XMCjobj.jpg', 8),
(33, 6, 'Selen Öztürk', 'actor', 'Gülfem Hatun', 'https://image.tmdb.org/t/p/w185/xgiTYVwDQA7jYDmnFdJWJS87PmD.jpg', 9),
(34, 6, 'Mehmet Günsür', 'actor', 'Şehzade Mustafa', 'https://image.tmdb.org/t/p/w185/wwIYzgt3lBDrqinpzdvcUGmlK6A.jpg', 10),
(35, 6, 'Aras Bulut İynemli', 'actor', 'Şehzade Bayezid', 'https://image.tmdb.org/t/p/w185/bAWgmcF26tPZv4gcwc9jmaNB9py.jpg', 11),
(36, 6, 'Selim Bayraktar', 'actor', 'Sümbül Ağa', 'https://image.tmdb.org/t/p/w185/2c5JSkUqHpClGIH6UjvFjrYsnne.jpg', 12),
(49, 12, 'Şafak Sezer', 'actor', 'Özgür', 'https://image.tmdb.org/t/p/w185/uQ2oPSDXl6P7TQptG5P9SBKc06K.jpg', 1),
(50, 12, 'Aydemir Akbaş', 'actor', 'Sabri', 'https://image.tmdb.org/t/p/w185/nYUNZDUgywwZV5IaQRhqY90kiSl.jpg', 2),
(51, 12, 'Ali Çatalbaş', 'actor', 'Tayfun', 'https://image.tmdb.org/t/p/w185/mlNGRuErTWSuzofyb46qoYAJP78.jpg', 3),
(52, 12, 'Ali Sürmeli', 'actor', 'Ateş', 'https://image.tmdb.org/t/p/w185/lkbbPGaKjQg0Q6pqHqs5SqGvsPB.jpg', 4),
(53, 12, 'Hakan Ural', 'actor', 'Sırrı', 'https://image.tmdb.org/t/p/w185/2DTkCx8OqMyF8olB2wnXRNw0Bxh.jpg', 5),
(54, 12, 'Eriş Akman', 'actor', '', 'https://image.tmdb.org/t/p/w185/mT4wL2vLN1TAWYDyFeIsHmMKmLN.jpg', 6),
(55, 12, 'Abidin Yerebakan', 'actor', 'Ekrem', 'https://image.tmdb.org/t/p/w185/13TNQmqhgbuJ29FmRJbVd789nvk.jpg', 7),
(56, 12, 'Ebubekir Öztürk', 'actor', 'Ganyotçu Arif', 'https://image.tmdb.org/t/p/w185/s3ssub94uX1HSrRsjGYeaziSf9v.jpg', 8),
(57, 12, 'Serkan Şengül', 'actor', 'Galerici Şahin', 'https://image.tmdb.org/t/p/w185/eRxCsC4LGRrl4zrvOyc7tdG2ayt.jpg', 9),
(58, 12, 'Ömer Kurt', 'actor', '', 'https://image.tmdb.org/t/p/w185/7t8kURZusKFnA9lGBiNnzxnjD0n.jpg', 10),
(59, 12, 'Hüseyin Elmalıpınar', 'actor', 'Hüseyin', 'https://image.tmdb.org/t/p/w185/igXMX6BsphLRzp4wbatdXurWZVb.jpg', 11),
(60, 12, 'Cenk Kangöz', 'actor', '', 'https://image.tmdb.org/t/p/w185/jb7EzllnlOxO8TvfUVP9sv97kqo.jpg', 12),
(61, 13, 'Çağatay Ulusoy', 'actor', 'Eşref', 'https://image.tmdb.org/t/p/w185/6anWOhuVKq64zoL2YCEN043B3SP.jpg', 1),
(62, 13, 'Demet Özdemir', 'actor', 'Nisan', 'https://image.tmdb.org/t/p/w185/nmy5ksmwyrdCLRaXb9DYA0pDEbh.jpg', 2),
(63, 13, 'Büşra Develi', 'actor', 'Çiğdem', 'https://image.tmdb.org/t/p/w185/3LJrnviAJtlEkkoDa2p0IzAAtJ5.jpg', 3),
(64, 13, 'Necip Memili', 'actor', 'Gürdal', 'https://image.tmdb.org/t/p/w185/d1jeBnsh30FkL5I196HHz85dz7h.jpg', 4),
(65, 13, 'Ahmet Rıfat Şungar', 'actor', 'Faruk', 'https://image.tmdb.org/t/p/w185/7tgV1HWmTkPNzhCLzjX0aVX7ZEA.jpg', 5),
(66, 13, 'Tolga Tekin', 'actor', 'Müslüm', 'https://image.tmdb.org/t/p/w185/ogGzsXFtay5XJMAXIMLCux5S8eZ.jpg', 6),
(67, 13, 'Görkem Sevindik', 'actor', 'Kadir Yanik', 'https://image.tmdb.org/t/p/w185/1228W2nEHGpOhvjkq8qRtlD1Bzu.jpg', 7),
(68, 13, 'Ceren Benderlioğlu', 'actor', 'Irmak Bozok', 'https://image.tmdb.org/t/p/w185/fa3fMnNEdaKiPLFyOZFUkvOAIZL.jpg', 8),
(69, 13, 'Kaan Turgut', 'actor', 'Kenan', 'https://image.tmdb.org/t/p/w185/mtG6ea2ZW9WRdTgoinafmMVbdn6.jpg', 9),
(70, 13, 'Cem Söküt', 'actor', 'Günahkar', 'https://image.tmdb.org/t/p/w185/iZ2CHiifsxtHm92dtz9pp8sz5Ud.jpg', 10),
(71, 13, 'Erol Babaoğlu', 'actor', 'Ölü Yaşar', 'https://image.tmdb.org/t/p/w185/7uJtpFnmC0b1quDuzrPmo34APlZ.jpg', 11),
(72, 13, 'Levent Özdilek', 'actor', 'Hıdır', 'https://image.tmdb.org/t/p/w185/jTdFl67jIGUYGCUBLTKJcx5kpGP.jpg', 12),
(73, 14, 'Deniz Can Aktaş', 'actor', 'Haydar Ali', 'https://image.tmdb.org/t/p/w185/2BiYbPCAosP81e1xffLDPMvEjtI.jpg', 1),
(74, 14, 'Uraz Kaygılaroğlu', 'actor', 'Bozkurt', 'https://image.tmdb.org/t/p/w185/7rdov56gMxgsBhOMeqdwv1cgOiW.jpg', 2),
(75, 14, 'Devrim Özkan', 'actor', 'Ceylan', 'https://image.tmdb.org/t/p/w185/12rRcA5jxTKyFnFoq26RpOam75a.jpg', 3),
(76, 14, 'Sümeyye Aydoğan', 'actor', 'Sultan', 'https://image.tmdb.org/t/p/w185/z5eA9gWnJIJR86gtSeG2vAPHkAN.jpg', 4),
(77, 14, 'Burak Sevinç', 'actor', 'Merdan', 'https://image.tmdb.org/t/p/w185/gnym6wXOpiwRvkdJZjSzfTWipWg.jpg', 5),
(78, 14, 'Ülkü Hilal Çiftçi', 'actor', 'Melek', 'https://image.tmdb.org/t/p/w185/v4U2Oby1nF6oInHqoLVVPaow3lp.jpg', 6),
(79, 14, 'Koray Şahinbaş', 'actor', 'Efraim', 'https://image.tmdb.org/t/p/w185/beSApM84qRpcx35YbGxQzQdfq6.jpg', 7),
(80, 14, 'Hülya Gülşen Irmak', 'actor', 'Gülsüm', 'https://image.tmdb.org/t/p/w185/oFHZsIcOU3mm37rHTTbzUv1F2cP.jpg', 8),
(81, 14, 'Ekin Mert Daymaz', 'actor', 'Ferhat', 'https://image.tmdb.org/t/p/w185/hcDkuSJAwsypq47dvCkc9QzNZ9N.jpg', 9),
(82, 14, 'Emir Benderlioğlu', 'actor', 'Yavuz', 'https://image.tmdb.org/t/p/w185/tOhlpQI14fQm2LplwhMYev2PWuc.jpg', 10),
(83, 14, 'Hakan Çelebi', 'actor', 'Paşa', 'https://image.tmdb.org/t/p/w185/1xo7eZhEL2OhkHuf8La1ilriBQm.jpg', 11),
(84, 14, 'Nilay Erdönmez', 'actor', 'Aslı', 'https://image.tmdb.org/t/p/w185/iD5Hhp4fewHEUIEe3kOMdAIh2Xe.jpg', 12),
(85, 15, 'Jet Li', 'actor', 'Rogue', 'https://image.tmdb.org/t/p/w185/c4s8INzU0ZAujCQ1YmphCmcsNzl.jpg', 1),
(86, 15, 'Jason Statham', 'actor', 'Special Agent Jack Crawford', 'https://image.tmdb.org/t/p/w185/pXGSq2UpcDE2NMF8LR56QZf5U1q.jpg', 2),
(87, 15, 'John Lone', 'actor', 'Li Chang', 'https://image.tmdb.org/t/p/w185/cO62O0mZGCvR0y5zM5oI2rUDhto.jpg', 3),
(88, 15, '石橋凌', 'actor', 'Shiro Yanagawa', 'https://image.tmdb.org/t/p/w185/1q8noUaI5E0ykjLdXRZcn7U79LN.jpg', 4),
(89, 15, 'Devon Aoki', 'actor', 'Kira Yanagawa', 'https://image.tmdb.org/t/p/w185/4ou67wYflxUuZCLUqK3vuKC13t9.jpg', 5),
(90, 15, '鄭浩南', 'actor', 'Wu Ti', 'https://image.tmdb.org/t/p/w185/bBL0SIcLDoytkoLCoMAUH2aBEf.jpg', 6),
(91, 15, 'Luis Guzmán', 'actor', 'Benny', 'https://image.tmdb.org/t/p/w185/kSdxUckOJj9R5VKrLUnRy14YhNV.jpg', 7),
(92, 15, 'Saul Rubinek', 'actor', 'Dr. Sherman', 'https://image.tmdb.org/t/p/w185/vW4ERsjupcc9u1ipDR7w6h6sphe.jpg', 8),
(93, 15, 'Nadine Velazquez', 'actor', 'Maria', 'https://image.tmdb.org/t/p/w185/uBWREzpphsFFJuVoT5LoQQ1WNTA.jpg', 9),
(94, 15, 'Kenneth Choi', 'actor', 'Takada', 'https://image.tmdb.org/t/p/w185/3606Uo4fiUtV9Y6eOwpM48qpSgJ.jpg', 10),
(95, 15, 'Andrea Roth', 'actor', 'Jenny Crawford', 'https://image.tmdb.org/t/p/w185/upv6wjBLhbHY4uadqEovtxvxVr4.jpg', 11),
(96, 15, 'Sung Kang', 'actor', 'Special Agent Goi', 'https://image.tmdb.org/t/p/w185/ox4ti0WmpJoN19n3iYJ2T2vHP5f.jpg', 12),
(97, 16, 'Sally Kirkland', 'actor', 'Elisabeth', 'https://image.tmdb.org/t/p/w185/bAjUA08z6k8FZ3bu5QWcMWWVdMQ.jpg', 1),
(98, 16, 'Natasha Alam', 'actor', 'Sara Ogden', 'https://image.tmdb.org/t/p/w185/ggG6h2fpTGaSKkSCCd6ruHFDJLK.jpg', 2),
(99, 16, 'Eric Roberts', 'actor', 'Parker', 'https://image.tmdb.org/t/p/w185/NiWg1TaUcal7xn1ZQeyA2dLd5F.jpg', 3),
(100, 16, 'Алексей Серебряков', 'actor', 'General Lvovsky', 'https://image.tmdb.org/t/p/w185/ch67oszo5D7lK64xnDp9JFcmGLp.jpg', 4),
(101, 16, 'Владимир Гостюхин', 'actor', 'Father Brothers (credit only)', 'https://image.tmdb.org/t/p/w185/mOhQTBVyAETCU1nPZcVjtQPAPoF.jpg', 5),
(102, 16, 'Игорь Савочкин', 'actor', 'Polkovnik Belousov', 'https://image.tmdb.org/t/p/w185/wWS0kDNnlBwTETbdTVaVeNVNJBh.jpg', 6),
(103, 16, 'Koby Azarly', 'actor', 'Suicide', '', 7),
(104, 16, 'Anthony De Longis', 'actor', 'Kidnapper', 'https://image.tmdb.org/t/p/w185/diSeADBcychtSiDsXlrpdth5u7f.jpg', 8),
(105, 17, 'Chris Hemsworth', 'actor', 'Mitch Nelson', 'https://image.tmdb.org/t/p/w185/piQGdoIQOF3C1EI5cbYZLAW1gfj.jpg', 1),
(106, 17, 'Michael Shannon', 'actor', 'Hal Spencer', 'https://image.tmdb.org/t/p/w185/6mMczfjM8CiS1WuBOgo5Xom1TcR.jpg', 2),
(107, 17, 'Michael Peña', 'actor', 'Sam Diller', 'https://image.tmdb.org/t/p/w185/afs4PCiwn8LR93a10drULLVeVLo.jpg', 3),
(108, 17, 'Navid Negahban', 'actor', 'General Dostum', 'https://image.tmdb.org/t/p/w185/pWi5tINf4mDvTYbNKZDJPODYKra.jpg', 4),
(109, 17, 'Trevante Rhodes', 'actor', 'Ben Milo', 'https://image.tmdb.org/t/p/w185/mx11nZOnT5nZbuzFi3TPVBQBZr.jpg', 5),
(110, 17, 'Geoff Stults', 'actor', 'Sean Coffers', 'https://image.tmdb.org/t/p/w185/815Unu0MUnwsmeAbjR2qNh9toSU.jpg', 6),
(111, 17, 'Thad Luckinbill', 'actor', 'Vern Michaels', 'https://image.tmdb.org/t/p/w185/7zBnpYTwwxKT6OHcycqacGQwQT8.jpg', 7),
(112, 17, 'Austin Stowell', 'actor', 'Fred Falls', 'https://image.tmdb.org/t/p/w185/At09XQpVXnChgedNsxu4ceR5W9i.jpg', 8),
(113, 17, 'Ben O\'Toole', 'actor', 'Scott Black', 'https://image.tmdb.org/t/p/w185/AwrcmZgIgBZCMU7PcVczarlo2VE.jpg', 9),
(114, 17, 'Jack Kesy', 'actor', 'Charles Jones', 'https://image.tmdb.org/t/p/w185/lQ8nUYK6InbCFk2TWNnXjjvG9IY.jpg', 10),
(115, 17, 'Fahim Fazli', 'actor', 'Commander Khaled', 'https://image.tmdb.org/t/p/w185/hNjSzr3quACIMwotRaRM2yKi9fU.jpg', 11),
(116, 17, 'Austin Hébert', 'actor', 'Pat Essex', 'https://image.tmdb.org/t/p/w185/vOuRMtOtPVd8vuNCCxxavQSdGIC.jpg', 12),
(117, 18, 'Chelsea Gilson', 'actor', 'Daniella Thompson', 'https://image.tmdb.org/t/p/w185/hlq8a03SBGgkkw021T9mAUOO4RI.jpg', 1),
(118, 18, 'Andrew Rogers', 'actor', 'James Miles', 'https://image.tmdb.org/t/p/w185/oSBBuN3SWGeYIsIm1RkyK7qWPLj.jpg', 2),
(119, 18, 'Johnny Pacar', 'actor', 'Dr. Chase Heibert', 'https://image.tmdb.org/t/p/w185/ge4ey7sLoLEEHW5sUfmDaU57SfE.jpg', 3),
(120, 18, 'Paul Logan', 'actor', 'Captain Pierce', 'https://image.tmdb.org/t/p/w185/ahVtxgcNuQm1js7gmbVR6tQYhTE.jpg', 4),
(121, 18, 'Mindy Montavon', 'actor', 'Dr. Michelle J.', 'https://image.tmdb.org/t/p/w185/3mA6xkdvEkb4zniiDtiXoKbiX5o.jpg', 5),
(122, 18, 'Jamel King', 'actor', 'Lt. Michaels', '', 6),
(123, 18, 'Vincent Duvall', 'actor', 'Captain Rodríguez', 'https://image.tmdb.org/t/p/w185/uDnAoEOGYEukZMnzwIusu93KQvJ.jpg', 7),
(124, 18, 'Lindsey Marie Wilson', 'actor', 'Dr. Ruth Robbins', 'https://image.tmdb.org/t/p/w185/hOyh74JlTniMotHWk6td26QHFrn.jpg', 8),
(125, 18, 'Lisa Cole', 'actor', 'Emily Williams', 'https://image.tmdb.org/t/p/w185/kYcY2Rd4vAxfSOG7l1iCl5atbBN.jpg', 9),
(126, 18, 'Bryan Scamman', 'actor', 'Ken Ortega', 'https://image.tmdb.org/t/p/w185/8lMC5A8ECRbC9IzkCFMhqyA6iQg.jpg', 10),
(127, 18, 'Stephen Wesley Green', 'actor', 'Lt. Green', '', 11),
(128, 18, 'Maye Harris', 'actor', 'Kimmy Heibert', '', 12),
(129, 19, 'Nicole Beharie', 'actor', 'Dee Roberts', 'https://image.tmdb.org/t/p/w185/7ZgUAk3hkCrWSfjLYvnbKdyaNiN.jpg', 1),
(130, 19, 'Tim Blake Nelson', 'actor', 'David Cohen', 'https://image.tmdb.org/t/p/w185/rWuTGiAMaaHIJ30eRkQS23LbRSW.jpg', 2),
(131, 19, 'Will Patton', 'actor', 'Sam Conroy', 'https://image.tmdb.org/t/p/w185/1V4DbU1mfEim4oaA5bvJywDAKwB.jpg', 3),
(132, 19, 'Michael O\'Keefe', 'actor', 'Calvin Beckett', 'https://image.tmdb.org/t/p/w185/wQsTL887RWvAJljzLhiPP52kjec.jpg', 4),
(133, 19, 'Malcolm Barrett', 'actor', 'Byron Hill', 'https://image.tmdb.org/t/p/w185/pKIBvaQsShrT9mXk7xUCQQ5TAvY.jpg', 5),
(134, 19, 'Xzibit', 'actor', 'Darrell Hughes', 'https://image.tmdb.org/t/p/w185/qhwJ7hZH4ErQSu2mXwSur4AAD0H.jpg', 6),
(135, 19, 'Charles S. Dutton', 'actor', 'Reverend Sanders', 'https://image.tmdb.org/t/p/w185/eNLs4JLILqSY50Tq5QMkrlBxD0H.jpg', 7),
(136, 19, 'Alfre Woodard', 'actor', 'Alma Roberts', 'https://image.tmdb.org/t/p/w185/jaojqEDdOBWfDpQfdWIbvyLDy5z.jpg', 8),
(137, 19, 'Tim Ware', 'actor', 'Mark Shelby', 'https://image.tmdb.org/t/p/w185/sZope09mLYOPaRT32DYIPxNH6Q1.jpg', 9),
(138, 19, 'Paul David Story', 'actor', 'David Higgins', 'https://image.tmdb.org/t/p/w185/sSLlBsb6nvW6pba2AveXU2Xu3Xv.jpg', 10),
(139, 19, 'David Warshofsky', 'actor', 'Robert Foster', 'https://image.tmdb.org/t/p/w185/pLABX1wyaVE4CcEZVmoM2Y5XbQU.jpg', 11),
(140, 19, 'Lucinda Jenney', 'actor', 'Leona Conroy', 'https://image.tmdb.org/t/p/w185/ahR0bNUJRi9Phpd7RZWx4ILiMEb.jpg', 12),
(141, 20, 'Liam Neeson', 'actor', 'Michael MacCauley', 'https://image.tmdb.org/t/p/w185/g0iIEyt9ILiKTG0g8K69US5VtLy.jpg', 1),
(142, 20, 'Patrick Wilson', 'actor', 'Alex Murphy', 'https://image.tmdb.org/t/p/w185/oym6H2QD9esk4yABjCHaUoNAOa8.jpg', 2),
(143, 20, 'Sam Neill', 'actor', 'Captain Hawthorne', 'https://image.tmdb.org/t/p/w185/iIfuxalf37xUayuGyK0zG7z6WEZ.jpg', 3),
(144, 20, 'Jonathan Banks', 'actor', 'Walt', 'https://image.tmdb.org/t/p/w185/bswk26L13PvY4iMTwUTAsepXCLv.jpg', 4),
(145, 20, 'Vera Farmiga', 'actor', 'Joanna', 'https://image.tmdb.org/t/p/w185/5Vs7huBmTKftwlsc2BPAntyaQYj.jpg', 5),
(146, 20, 'Elizabeth McGovern', 'actor', 'Karen MacCauley', 'https://image.tmdb.org/t/p/w185/ihYdCKyr3JPz74tPuvkn1WSNh9b.jpg', 6),
(147, 20, 'Killian Scott', 'actor', 'Dylan', 'https://image.tmdb.org/t/p/w185/8ri6qJgCitrezFaiCPzgQbdDBYp.jpg', 7),
(148, 20, 'Shazad Latif', 'actor', 'Vince', 'https://image.tmdb.org/t/p/w185/n8vJ1AHhv294hGqZxhjM95OvpxT.jpg', 8),
(149, 20, 'Andy Nyman', 'actor', 'Tony', 'https://image.tmdb.org/t/p/w185/9bN9RVoPWmsmV3VBI7hp4VKD9Kg.jpg', 9),
(150, 20, 'Colin McFarlane', 'actor', 'Conductor Sam', 'https://image.tmdb.org/t/p/w185/tCLiWpKooSQJfyC260qNeNYTe9S.jpg', 10),
(151, 20, 'Roland Møller', 'actor', 'Jackson', 'https://image.tmdb.org/t/p/w185/bF7wrJ5mrIhSwyi6ylhyQWj9BoN.jpg', 11),
(152, 20, 'Florence Pugh', 'actor', 'Gwen', 'https://image.tmdb.org/t/p/w185/wcDXv5Oc8w01QfxJMla96xIDROT.jpg', 12),
(153, 21, 'Jason Statham', 'actor', 'Frank Martin', 'https://image.tmdb.org/t/p/w185/pXGSq2UpcDE2NMF8LR56QZf5U1q.jpg', 1),
(154, 21, 'Natalya Rudakova', 'actor', 'Valentina Vasilev', 'https://image.tmdb.org/t/p/w185/7439tb7LAfZGRtZq2GyMts8RKDn.jpg', 2),
(155, 21, 'François Berléand', 'actor', 'Inspector Tarconi', 'https://image.tmdb.org/t/p/w185/5TDYSUfCj8vaG5sqJXUU06Cvafa.jpg', 3),
(156, 21, 'Robert Knepper', 'actor', 'Johnson', 'https://image.tmdb.org/t/p/w185/lRncjvgCIm1muIkK94zJSH2i3d6.jpg', 4),
(157, 21, 'Jeroen Krabbé', 'actor', 'Leonid Vasilev', 'https://image.tmdb.org/t/p/w185/r26lZKUeLIaUt8DMotm4mdc4EaY.jpg', 5),
(158, 21, 'Alex Kobold', 'actor', 'Leonid\'s Aide', 'https://image.tmdb.org/t/p/w185/7genXa9tf0XeQu8sAxYWXBgLBaC.jpg', 6),
(159, 21, 'David Atrakchi', 'actor', 'Malcom Manville', 'https://image.tmdb.org/t/p/w185/t1MVg0SnYLGdl6PTkUUTPQB1ktb.jpg', 7),
(160, 21, 'Yann Sundberg', 'actor', 'Flag', 'https://image.tmdb.org/t/p/w185/qel4QZT49XLvi5hjQv6iPqyv2a4.jpg', 8),
(161, 21, 'Ériq Ebouaney', 'actor', 'Ice', 'https://image.tmdb.org/t/p/w185/7x8P4IL9DnVlsAB5KFmMB9gfzLX.jpg', 9),
(162, 21, 'David Kammenos', 'actor', 'Driver Market', 'https://image.tmdb.org/t/p/w185/mRDIDNzrLGU6sUS8SApjMEvmFjq.jpg', 10),
(163, 21, 'Silvio Simac', 'actor', 'Mighty Joe', 'https://image.tmdb.org/t/p/w185/fhmCo9mQplGeygaFyubqG61SJ6M.jpg', 11),
(164, 21, 'Oscar Relier', 'actor', 'Thug / Driver', 'https://image.tmdb.org/t/p/w185/jH9Kxbp5725uqgT4kWiM326IdAM.jpg', 12),
(165, 24, '花澤香菜', 'actor', 'Naho Takamiya (voice)', 'https://image.tmdb.org/t/p/w185/9UTBlNRopSOKyoWnCm74tyHOfR1.jpg', 1),
(166, 24, '山下誠一郎', 'actor', 'Kakeru Naruse (voice)', 'https://image.tmdb.org/t/p/w185/zym6WY3rxL4FdbHoLzG2CnqnR7.jpg', 2),
(167, 24, '古川慎', 'actor', 'Hiroto Suwa (voice)', 'https://image.tmdb.org/t/p/w185/inLmBZhrqXeE9wlViyK28ocKJSw.jpg', 3),
(168, 24, '高森奈津美', 'actor', 'Azusa \'Azu\' Murasaka (voice)', 'https://image.tmdb.org/t/p/w185/lmoEgs5x8NchMlsXqmU4ZYsC8ey.jpg', 4),
(169, 24, '興津和幸', 'actor', 'Saku Hagita (voice)', 'https://image.tmdb.org/t/p/w185/vgyaK5dAxhvzAi6LJM3ZyaJd4mJ.jpg', 5),
(170, 24, '衣川里佳', 'actor', 'Takako \'Taka\' Chino (voice)', 'https://image.tmdb.org/t/p/w185/sm99XGd4upB4iujtg7OThvKcjpH.jpg', 6),
(171, 26, '梶裕貴', 'actor', 'Eren Yeager (voice)', 'https://image.tmdb.org/t/p/w185/8wKdPV11IwowfwoqGqMMNt9hmp6.jpg', 1),
(172, 26, '石川由依', 'actor', 'Mikasa Ackerman (voice)', 'https://image.tmdb.org/t/p/w185/2y1y1W4q8UIR0Vbs4NNvc7722XT.jpg', 2),
(173, 26, '井上麻里奈', 'actor', 'Armin Arlert (voice)', 'https://image.tmdb.org/t/p/w185/nQ7a3krkbea6ukw9KGvvTpMma96.jpg', 3),
(174, 26, '谷山紀章', 'actor', 'Jean Kirstein (voice)', 'https://image.tmdb.org/t/p/w185/9lOnUF6C6xEhZ0FQmYNzNZhcUpd.jpg', 4),
(175, 26, '下野紘', 'actor', 'Connie Springer (voice)', 'https://image.tmdb.org/t/p/w185/yrSDcgFefHtWkFmLnTrcw2t0MV.jpg', 5),
(176, 26, '細谷佳正', 'actor', 'Reiner Braun (voice)', 'https://image.tmdb.org/t/p/w185/lUR5oN1LrqGgp25IOcI1qOH1Ud5.jpg', 6),
(177, 26, '子安武人', 'actor', 'Zeke (voice)', 'https://image.tmdb.org/t/p/w185/8uBkNDKPNmp9JWgMUI02NVyfhi1.jpg', 7),
(178, 26, '佐倉綾音', 'actor', 'Gabi Braun (voice)', 'https://image.tmdb.org/t/p/w185/yPbTmntASE9psPIMhNGU5oo6vIH.jpg', 8),
(179, 26, '花江夏樹', 'actor', 'Falco Grice (voice)', 'https://image.tmdb.org/t/p/w185/alTb0DlcPIbcwM08WSmxFai58sd.jpg', 9);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `content_categories`
--

CREATE TABLE `content_categories` (
  `content_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `content_categories`
--

INSERT INTO `content_categories` (`content_id`, `category_id`) VALUES
(4, 21),
(6, 1),
(6, 21),
(7, 21),
(7, 22),
(7, 26),
(7, 27),
(10, 17),
(10, 21),
(10, 22),
(10, 25),
(10, 26),
(10, 27),
(12, 16),
(12, 24),
(12, 25),
(13, 2),
(13, 9),
(13, 22),
(13, 25),
(13, 27),
(14, 2),
(14, 9),
(14, 17),
(14, 25),
(14, 26),
(14, 27),
(15, 2),
(15, 10),
(15, 17),
(15, 24),
(15, 25),
(15, 27),
(15, 29),
(16, 2),
(16, 11),
(16, 24),
(16, 25),
(16, 26),
(16, 29),
(17, 2),
(17, 24),
(17, 26),
(17, 29),
(17, 30),
(18, 24),
(18, 29),
(18, 30),
(19, 9),
(19, 17),
(19, 25),
(19, 26),
(19, 27),
(19, 30),
(20, 2),
(20, 7),
(20, 10),
(20, 17),
(20, 24),
(20, 25),
(20, 29),
(20, 30),
(21, 2),
(21, 7),
(21, 10),
(21, 24),
(21, 25),
(21, 29),
(21, 30),
(22, 6),
(22, 21),
(23, 6),
(24, 3),
(24, 28),
(26, 28);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `content_links`
--

CREATE TABLE `content_links` (
  `id` int(11) NOT NULL,
  `content_id` int(11) NOT NULL,
  `link_type` enum('trailer','movie_file','youtube_movie') NOT NULL DEFAULT 'trailer',
  `label` varchar(100) DEFAULT NULL,
  `url` text NOT NULL,
  `sort_order` int(3) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `content_links`
--

INSERT INTO `content_links` (`id`, `content_id`, `link_type`, `label`, `url`, `sort_order`, `created_at`) VALUES
(7, 4, 'trailer', NULL, 'https://www.youtube.com/watch?v=RwP-qq8jqHw', 0, '2026-05-22 02:21:50'),
(12, 10, 'trailer', NULL, 'https://youtu.be/7NuNSytqSUk?si=v0R3KSNEr2UQ_Xux', 0, '2026-05-28 00:34:49'),
(17, 7, 'trailer', NULL, 'https://youtu.be/5XuBa9Cosq0?si=M_2JasPGUZtQDNj0', 0, '2026-05-28 00:52:59'),
(18, 6, 'trailer', NULL, 'https://youtu.be/DMwrtwQtu0Q?si=L-Kp6L4lsnqzxy5h', 0, '2026-05-28 00:54:16'),
(27, 12, 'trailer', NULL, 'https://youtu.be/QpnK_1bYiDE?si=24TlE2UFpM05YO6x', 0, '2026-05-28 01:22:28'),
(28, 12, '', NULL, 'https://youtu.be/pZ8N0bt8Rw0?si=bFOi6s0rqlr9HuzD', 0, '2026-05-28 01:22:28'),
(30, 13, 'trailer', NULL, 'https://youtu.be/EhMyPcygqLk?si=dDrJ3KZUbx6qMFhB', 0, '2026-06-05 00:14:24'),
(33, 14, 'trailer', NULL, 'https://youtu.be/0_wGcmuP8yE?si=3cDSccYseEHGXVdi', 0, '2026-06-05 00:28:22'),
(34, 15, 'trailer', NULL, 'https://youtu.be/5dfcQOBJ83A?si=Q9miAzLxLLnNHyDZ', 0, '2026-06-05 00:36:22'),
(35, 15, '', NULL, 'https://youtu.be/PUUExpMqYU8?si=SWmTZBvfczEGixUk', 0, '2026-06-05 00:36:22'),
(36, 16, 'trailer', NULL, 'https://youtu.be/fIXwJ9Q4dAQ?si=s_Q3tDA28YF7TFBX', 0, '2026-06-05 00:59:53'),
(37, 16, '', NULL, 'https://youtu.be/38-lofbhB4Q?si=ZAsEU1IS5kK9tOIb', 0, '2026-06-05 00:59:53'),
(38, 17, 'trailer', NULL, 'https://youtu.be/FPduxRAdOJ4?si=4ERFTxv1LeARlMSC', 0, '2026-06-05 01:11:47'),
(39, 17, '', NULL, 'https://youtu.be/p7pkxyOcq2E?si=rYm9V9sdyH1aVEWA', 0, '2026-06-05 01:11:47'),
(43, 18, 'trailer', NULL, 'https://youtu.be/44CmaeYy7tY?si=E2r88eANj4-x5kVq', 0, '2026-06-05 01:15:52'),
(44, 18, '', NULL, 'https://www.youtube.com/watch?v=00Mil5qOfSo', 0, '2026-06-05 01:15:52'),
(45, 19, 'trailer', NULL, 'https://www.youtube.com/watch?v=Qv8Jq09qU1Q', 0, '2026-06-05 01:18:08'),
(46, 19, '', NULL, 'https://www.youtube.com/watch?v=6-dCb-eK8zI', 0, '2026-06-05 01:18:08'),
(47, 20, 'trailer', NULL, 'https://www.youtube.com/watch?v=aDshY43Ol2U', 0, '2026-06-05 01:22:26'),
(48, 20, '', NULL, 'https://www.youtube.com/watch?v=-3mZHLFwAsk', 0, '2026-06-05 01:22:26'),
(49, 21, 'trailer', NULL, 'https://www.youtube.com/watch?v=Pbh3CDBNIQA', 0, '2026-06-05 01:25:36'),
(50, 21, '', NULL, 'https://www.youtube.com/watch?v=rbkZQitHPaU', 0, '2026-06-05 01:25:36'),
(51, 23, '', NULL, 'https://www.youtube.com/watch?v=Wh1pN1-UCis', 0, '2026-06-05 01:46:55'),
(52, 24, 'trailer', NULL, 'https://www.youtube.com/watch?v=kVDqRG0qbBs', 0, '2026-06-05 01:49:16'),
(53, 26, 'trailer', NULL, 'https://www.youtube.com/watch?v=LV-nazLVmgo', 0, '2026-06-05 01:52:32');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `episodes`
--

CREATE TABLE `episodes` (
  `id` int(11) NOT NULL,
  `content_id` int(11) NOT NULL,
  `season_number` int(3) NOT NULL DEFAULT 1,
  `episode_number` int(3) NOT NULL DEFAULT 1,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `video_url` text DEFAULT NULL,
  `thumbnail` varchar(255) DEFAULT NULL,
  `duration` varchar(20) DEFAULT NULL,
  `intro_start_seconds` int(5) DEFAULT NULL,
  `intro_end_seconds` int(5) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `episodes`
--

INSERT INTO `episodes` (`id`, `content_id`, `season_number`, `episode_number`, `title`, `description`, `video_url`, `thumbnail`, `duration`, `intro_start_seconds`, `intro_end_seconds`, `created_at`) VALUES
(4, 4, 1, 1, '', NULL, 'https://www.youtube.com/watch?v=BcUckd8Ox9I', NULL, NULL, NULL, NULL, '2026-05-22 01:25:44'),
(5, 4, 2, 1, '2. Sezon', NULL, 'https://www.youtube.com/watch?v=doTKxutg1R0', NULL, NULL, NULL, NULL, '2026-05-22 03:50:37'),
(6, 6, 1, 1, '', NULL, 'https://youtu.be/JBaYiSyH3CY?si=AMN0S7Tz53sozSH5', NULL, NULL, NULL, NULL, '2026-05-22 03:57:27'),
(7, 7, 1, 1, '', NULL, 'https://youtu.be/TBBRBD8Fudg?si=olmbrYy6NNPNM8wB', NULL, NULL, 1, 30, '2026-05-27 23:57:11'),
(8, 10, 1, 1, '', NULL, 'https://youtu.be/Sb71iNewUJ8?si=AOGtGiDfOmHTTe7R', NULL, NULL, NULL, NULL, '2026-05-28 00:36:17'),
(9, 13, 1, 1, '', NULL, 'https://youtu.be/IWZr0PdZfSM?si=VCIJavbkM9SW9PIF', NULL, NULL, NULL, NULL, '2026-06-05 00:16:41'),
(11, 14, 1, 1, '', NULL, 'https://youtu.be/Pgeqp2krn1g?si=14l_BRawOyJJ3vMk', NULL, NULL, NULL, NULL, '2026-06-05 00:32:35'),
(12, 22, 1, 1, '', NULL, 'https://www.youtube.com/watch?v=Pp3CcQi0XQA', NULL, NULL, NULL, NULL, '2026-06-05 01:40:48'),
(13, 22, 1, 2, '', NULL, 'https://www.youtube.com/watch?v=moHNy5mWCQg', NULL, NULL, NULL, NULL, '2026-06-05 01:41:13'),
(14, 22, 1, 3, '', NULL, 'https://www.youtube.com/watch?v=UHYmS1x1Uvc', NULL, NULL, NULL, NULL, '2026-06-05 01:41:33'),
(15, 24, 1, 1, '', NULL, 'https://www.youtube.com/watch?v=kvgp6mpBOdQ&list=PLUc0tnZusNmxXGmzbyzJPRvJiXnpEwCE0&index=1', NULL, NULL, NULL, NULL, '2026-06-05 01:50:15'),
(16, 24, 1, 2, '', NULL, 'https://www.youtube.com/watch?v=RhUCjpKnz4c&list=PLUc0tnZusNmxXGmzbyzJPRvJiXnpEwCE0&index=2', NULL, NULL, NULL, NULL, '2026-06-05 01:51:00'),
(17, 24, 1, 3, '', NULL, 'https://www.youtube.com/watch?v=mtnw_9iiU6I&list=PLUc0tnZusNmxXGmzbyzJPRvJiXnpEwCE0&index=3', NULL, NULL, NULL, NULL, '2026-06-05 01:51:18'),
(18, 26, 1, 1, '', NULL, 'https://www.youtube.com/watch?v=yhFPJ2xFGS8', NULL, NULL, NULL, NULL, '2026-06-05 01:52:51');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `likes`
--

CREATE TABLE `likes` (
  `id` int(11) NOT NULL,
  `profile_id` int(11) NOT NULL,
  `content_id` int(11) NOT NULL,
  `is_like` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `likes`
--

INSERT INTO `likes` (`id`, `profile_id`, `content_id`, `is_like`, `created_at`) VALUES
(1, 2, 4, 1, '2026-05-22 02:33:30');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `expected_date` date DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `notifications`
--

INSERT INTO `notifications` (`id`, `title`, `message`, `image`, `expected_date`, `is_active`, `created_at`) VALUES
(1, 'Spider Noir', '', 'notif_1780609699_786.jpg', '0000-00-00', 1, '2026-06-05 00:48:19');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `profiles`
--

CREATE TABLE `profiles` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `profile_name` varchar(50) NOT NULL,
  `avatar_image` varchar(255) DEFAULT 'default-avatar.png',
  `is_child` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `profiles`
--

INSERT INTO `profiles` (`id`, `user_id`, `profile_name`, `avatar_image`, `is_child`, `created_at`) VALUES
(1, 1, 'Admin', 'default-avatar.png', 0, '2026-05-22 00:24:12'),
(2, 1, 'Mehmet', 'avatar_2_1779399031.jpg', 0, '2026-05-22 00:29:07');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `subscriptions`
--

CREATE TABLE `subscriptions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `plan_name` varchar(100) NOT NULL DEFAULT 'Deneme AboneliÄŸi',
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` enum('active','cancelled','expired') NOT NULL DEFAULT 'active',
  `start_date` datetime NOT NULL DEFAULT current_timestamp(),
  `end_date` datetime DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT 'Ãœcretsiz Deneme',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `subscriptions`
--

INSERT INTO `subscriptions` (`id`, `user_id`, `plan_name`, `price`, `status`, `start_date`, `end_date`, `payment_method`, `created_at`) VALUES
(1, 1, 'Premium', 0.00, 'active', '2026-05-22 00:24:12', NULL, 'Sistem', '2026-05-22 00:24:12');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') NOT NULL DEFAULT 'user',
  `subscription_status` enum('active','inactive','expired') NOT NULL DEFAULT 'active',
  `notifications_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_token_expiry` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `subscription_status`, `notifications_enabled`, `reset_token`, `reset_token_expiry`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'admin@movify.com', '$2y$10$hb1aCv50Wn8182.sf7Fr3O.9GqjjlSHSaXVwnLozY/ZkpH784hxb.', 'admin', 'active', 1, NULL, NULL, '2026-05-22 00:24:12', '2026-05-22 00:27:57');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `watchlist`
--

CREATE TABLE `watchlist` (
  `id` int(11) NOT NULL,
  `profile_id` int(11) NOT NULL,
  `content_id` int(11) NOT NULL,
  `added_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `watchlist`
--

INSERT INTO `watchlist` (`id`, `profile_id`, `content_id`, `added_at`) VALUES
(3, 2, 10, '2026-05-28 00:35:30'),
(4, 2, 21, '2026-06-05 01:54:00'),
(5, 2, 26, '2026-06-05 01:54:05'),
(6, 2, 24, '2026-06-05 01:54:11'),
(7, 2, 19, '2026-06-05 01:54:17'),
(8, 2, 7, '2026-06-05 01:54:30');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `watch_history`
--

CREATE TABLE `watch_history` (
  `id` int(11) NOT NULL,
  `profile_id` int(11) NOT NULL,
  `content_id` int(11) NOT NULL,
  `episode_id` int(11) DEFAULT NULL,
  `watched_position_seconds` int(11) NOT NULL DEFAULT 0,
  `total_duration_seconds` int(11) NOT NULL DEFAULT 0,
  `is_finished` tinyint(1) NOT NULL DEFAULT 0,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `watch_history`
--

INSERT INTO `watch_history` (`id`, `profile_id`, `content_id`, `episode_id`, `watched_position_seconds`, `total_duration_seconds`, `is_finished`, `updated_at`) VALUES
(1, 2, 4, 4, 177, 2300, 0, '2026-06-05 01:53:21'),
(2, 2, 7, 7, 1336, 4638, 0, '2026-05-28 01:55:20'),
(3, 2, 12, NULL, 1, 5357, 0, '2026-06-04 23:26:05'),
(4, 2, 10, 8, 1640, 5281, 0, '2026-06-05 00:00:59'),
(5, 2, 13, 9, 1720, 8231, 0, '2026-06-05 00:17:48'),
(6, 2, 15, NULL, 1216, 6699, 0, '2026-06-05 00:37:55'),
(7, 2, 16, NULL, 2665, 5610, 0, '2026-06-05 01:00:23'),
(8, 2, 17, NULL, 1631, 7774, 0, '2026-06-05 01:12:06'),
(9, 2, 18, NULL, 1258, 4943, 0, '2026-06-05 01:16:07'),
(10, 2, 19, NULL, 424, 6182, 0, '2026-06-05 01:19:02'),
(11, 2, 21, NULL, 5, 5995, 0, '2026-06-05 01:25:56'),
(12, 2, 22, 13, 1, 1364, 0, '2026-06-05 01:42:07'),
(13, 2, 22, 12, 2, 1263, 0, '2026-06-05 01:42:14'),
(14, 2, 23, NULL, 2, 4314, 0, '2026-06-05 01:47:15'),
(15, 2, 24, 15, 4, 1428, 0, '2026-06-05 01:50:31'),
(16, 2, 24, 17, 364, 1429, 0, '2026-06-05 01:51:31'),
(17, 2, 26, 18, 2, 1541, 0, '2026-06-05 01:53:08');

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Tablo için indeksler `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_comments_content` (`content_id`),
  ADD KEY `fk_comments_profile` (`profile_id`);

--
-- Tablo için indeksler `content`
--
ALTER TABLE `content`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `content` ADD FULLTEXT KEY `ft_content_title` (`title`);
ALTER TABLE `content` ADD FULLTEXT KEY `ft_content_search` (`title`,`description`);

--
-- Tablo için indeksler `content_cast`
--
ALTER TABLE `content_cast`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_cast_content` (`content_id`);

--
-- Tablo için indeksler `content_categories`
--
ALTER TABLE `content_categories`
  ADD PRIMARY KEY (`content_id`,`category_id`),
  ADD KEY `fk_cc_category` (`category_id`);

--
-- Tablo için indeksler `content_links`
--
ALTER TABLE `content_links`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_cl_content` (`content_id`);

--
-- Tablo için indeksler `episodes`
--
ALTER TABLE `episodes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_episodes_content` (`content_id`);

--
-- Tablo için indeksler `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_like` (`profile_id`,`content_id`),
  ADD KEY `fk_likes_content` (`content_id`);

--
-- Tablo için indeksler `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `profiles`
--
ALTER TABLE `profiles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_profiles_user` (`user_id`);

--
-- Tablo için indeksler `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_subscriptions_user` (`user_id`);

--
-- Tablo için indeksler `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Tablo için indeksler `watchlist`
--
ALTER TABLE `watchlist`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_watchlist` (`profile_id`,`content_id`),
  ADD KEY `fk_wl_content` (`content_id`);

--
-- Tablo için indeksler `watch_history`
--
ALTER TABLE `watch_history`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_watch` (`profile_id`,`content_id`,`episode_id`),
  ADD KEY `fk_wh_content` (`content_id`),
  ADD KEY `fk_wh_episode` (`episode_id`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- Tablo için AUTO_INCREMENT değeri `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `content`
--
ALTER TABLE `content`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- Tablo için AUTO_INCREMENT değeri `content_cast`
--
ALTER TABLE `content_cast`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=180;

--
-- Tablo için AUTO_INCREMENT değeri `content_links`
--
ALTER TABLE `content_links`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- Tablo için AUTO_INCREMENT değeri `episodes`
--
ALTER TABLE `episodes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Tablo için AUTO_INCREMENT değeri `likes`
--
ALTER TABLE `likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Tablo için AUTO_INCREMENT değeri `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Tablo için AUTO_INCREMENT değeri `profiles`
--
ALTER TABLE `profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Tablo için AUTO_INCREMENT değeri `subscriptions`
--
ALTER TABLE `subscriptions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Tablo için AUTO_INCREMENT değeri `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Tablo için AUTO_INCREMENT değeri `watchlist`
--
ALTER TABLE `watchlist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Tablo için AUTO_INCREMENT değeri `watch_history`
--
ALTER TABLE `watch_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Dökümü yapılmış tablolar için kısıtlamalar
--

--
-- Tablo kısıtlamaları `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `fk_comments_content` FOREIGN KEY (`content_id`) REFERENCES `content` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_comments_profile` FOREIGN KEY (`profile_id`) REFERENCES `profiles` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `content_cast`
--
ALTER TABLE `content_cast`
  ADD CONSTRAINT `fk_cast_content` FOREIGN KEY (`content_id`) REFERENCES `content` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `content_categories`
--
ALTER TABLE `content_categories`
  ADD CONSTRAINT `fk_cc_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_cc_content` FOREIGN KEY (`content_id`) REFERENCES `content` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `content_links`
--
ALTER TABLE `content_links`
  ADD CONSTRAINT `fk_cl_content` FOREIGN KEY (`content_id`) REFERENCES `content` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `episodes`
--
ALTER TABLE `episodes`
  ADD CONSTRAINT `fk_episodes_content` FOREIGN KEY (`content_id`) REFERENCES `content` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `likes`
--
ALTER TABLE `likes`
  ADD CONSTRAINT `fk_likes_content` FOREIGN KEY (`content_id`) REFERENCES `content` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_likes_profile` FOREIGN KEY (`profile_id`) REFERENCES `profiles` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `profiles`
--
ALTER TABLE `profiles`
  ADD CONSTRAINT `fk_profiles_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD CONSTRAINT `fk_subscriptions_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `watchlist`
--
ALTER TABLE `watchlist`
  ADD CONSTRAINT `fk_wl_content` FOREIGN KEY (`content_id`) REFERENCES `content` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_wl_profile` FOREIGN KEY (`profile_id`) REFERENCES `profiles` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `watch_history`
--
ALTER TABLE `watch_history`
  ADD CONSTRAINT `fk_wh_content` FOREIGN KEY (`content_id`) REFERENCES `content` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_wh_episode` FOREIGN KEY (`episode_id`) REFERENCES `episodes` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_wh_profile` FOREIGN KEY (`profile_id`) REFERENCES `profiles` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
