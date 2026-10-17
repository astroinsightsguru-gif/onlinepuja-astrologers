import 'dart:async';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter_tts/flutter_tts.dart';
import 'package:url_launcher/url_launcher.dart';

import '../japa/japa_mala_screen.dart';

class VedicVerse {
  final String sanskrit;
  final String englishMeaning;
  final String? hindiMeaning;

  const VedicVerse({
    required this.sanskrit,
    required this.englishMeaning,
    this.hindiMeaning,
  });
}

class VedicTrack {
  final String id;
  final String title;
  final String hindiTitle;
  final String category;
  final String deity;
  final String coverEmoji;
  final List<Color> gradientColors;
  final List<VedicVerse> verses;
  final String fullTtsScript;

  const VedicTrack({
    required this.id,
    required this.title,
    required this.hindiTitle,
    required this.category,
    required this.deity,
    required this.coverEmoji,
    required this.gradientColors,
    required this.verses,
    required this.fullTtsScript,
  });
}

/// Vedic Audio OTT: Dedicated Sacred Chanting & Aarti Sanctum
class VedicAudioScreen extends StatefulWidget {
  const VedicAudioScreen({super.key, this.initialTrackId});

  static const route = '/vedic-audio';
  final String? initialTrackId;

  @override
  State<VedicAudioScreen> createState() => _VedicAudioScreenState();
}

class _VedicAudioScreenState extends State<VedicAudioScreen> {
  final FlutterTts _tts = FlutterTts();
  late VedicTrack _currentTrack;
  bool _isPlaying = false;
  int _activeVerseIndex = 0;
  int _loopTarget = 108;
  int _completedLoops = 0;
  final ScrollController _verseScroll = ScrollController();
  Timer? _verseTimer;

  static const List<VedicTrack> tracks = [
    VedicTrack(
      id: 'hanuman_chalisa',
      title: 'Shri Hanuman Chalisa',
      hindiTitle: 'श्री हनुमान चालीसा (सम्पूर्ण)',
      category: 'Stotram & Chalisa',
      deity: 'Lord Hanuman (Mahaveer)',
      coverEmoji: '🚩',
      gradientColors: [Color(0xFFB45309), Color(0xFFEA580C)],
      verses: [
        VedicVerse(
          sanskrit: '॥ दोहा ॥\nश्रीगुरु चरन सरोज रज निज मनु मुकुरु सुधारि।\nबरनऊँ रघुबर बिमल जसु जो दायकु फल चारि॥',
          englishMeaning: 'Cleansing the mirror of my mind with the dust from the lotus feet of my Guru, I describe the pure glory of Lord Rama, which bestows the four fruits of life.',
          hindiMeaning: 'गुरुदेव के चरण कमलों की रज से अपने मन रूपी दर्पण को स्वच्छ कर, मैं भगवान श्री राम के निर्मल यश का गान करता हूँ।',
        ),
        VedicVerse(
          sanskrit: 'बुद्धिहीन तनु जानिके सुमिरौं पवन-कुमार।\nबल बुद्धि बिद्या देहु मोहिं हरहु कलेस बिकार॥',
          englishMeaning: 'Knowing myself to be ignorant, I remember You, O Son of Wind (Pawan-Kumar)! Grant me physical strength, wisdom, and right knowledge, and remove all my afflictions.',
          hindiMeaning: 'हे पवनपुत्र, मुझे ज्ञानहीन जानकर बल, बुद्धि और विद्या प्रदान करें तथा मेरे सभी दुखों को दूर करें।',
        ),
        VedicVerse(
          sanskrit: 'जय हनुमान ज्ञान गुन सागर।\nजय कपीस तिहुँ लोक उजागर॥\nराम दूत अतुलित बल धामा।\nअंजनि-पुत्र पवनसुत नामा॥',
          englishMeaning: 'Victory to Lord Hanuman, the boundless ocean of wisdom and virtues. Victory to the Lord of monkeys who illuminates the three worlds! Messenger of Rama, embodiment of unmatched strength, known as Anjani’s son and Pawanputra.',
          hindiMeaning: 'ज्ञान और गुणों के सागर श्री हनुमान की जय हो। तीनों लोकों को प्रकाशित करने वाले हे कपीश, आपकी जय हो।',
        ),
        VedicVerse(
          sanskrit: 'महाबीर बिक्रम बजरंगी।\nकुमति निवार सुमति के संगी॥\nकंचन बरन बिराज सुबेसा।\nकानन कुंडल कुंचित केसा॥',
          englishMeaning: 'O mighty Mahaveer, with thunderbolt-like body! You dispel evil thoughts and accompany the wise. Your golden complexion radiates brilliantly, adorned with earrings and curly locks.',
          hindiMeaning: 'हे परम पराक्रमी वज्र समान देह वाले वीर, आप दुर्बुद्धि का नाश कर सुबुद्धि प्रदान करते हैं।',
        ),
        VedicVerse(
          sanskrit: 'संकट कटै मिटै सब पीरा।\nजो सुमिरै हनुमत बलबीरा॥\nजय जय जय हनुमान गोसाईं।\nकृपा करहु गुरुदेव की नाईं॥',
          englishMeaning: 'All distress is severed and all pain dissolves for one who sincerely remembers the valiant Hanuman! Hail, hail, hail Lord Hanuman! Bestow your divine grace upon me like a compassionate Guru.',
          hindiMeaning: 'श्री बलवीर हनुमान का स्मरण करने से समस्त संकट कट जाते हैं और सारी पीड़ाएं नष्ट हो जाती हैं।',
        ),
        VedicVerse(
          sanskrit: '॥ दोहा ॥\nपवनतनय संकट हरन मंगल मूरति रूप।\nराम लखन सीता सहित हृदय बसहु सुर भूप॥',
          englishMeaning: 'O Son of Wind, reliever of all sorrows, embodiment of all auspiciousness! Along with Lord Rama, Lakshmana, and Mother Sita, please reside forever in my heart, O King of Gods!',
          hindiMeaning: 'हे संकटमोचन पवनपुत्र, मंगल की मूरत! आप श्री राम, लक्ष्मण और माता सीता सहित सदैव मेरे हृदय में वास करें।',
        ),
      ],
      fullTtsScript: 'श्रीगुरु चरन सरोज रज निज मनु मुकुरु सुधारि। बरनऊँ रघुबर बिमल जसु जो दायकु फल चारि। बुद्धिहीन तनु जानिके सुमिरौं पवन कुमार। बल बुद्धि बिद्या देहु मोहिं हरहु कलेस बिकार। जय हनुमान ज्ञान गुन सागर। जय कपीस तिहुँ लोक उजागर। राम दूत अतुलित बल धामा। अंजनि पुत्र पवनसुत नामा। संकट कटै मिटै सब पीरा। जो सुमिरै हनुमत बलबीरा। पवनतनय संकट हरन मंगल मूरति रूप। राम लखन सीता सहित हृदय बसहु सुर भूप।',
    ),
    VedicTrack(
      id: 'mahamrityunjaya',
      title: 'Maha Mrityunjaya Mantra (108x)',
      hindiTitle: 'महामृत्युंजय मंत्र (संजीवनी जप)',
      category: 'Vedic Healing Mantra',
      deity: 'Lord Shiva (Mahadev)',
      coverEmoji: '🔱',
      gradientColors: [Color(0xFF0F172A), Color(0xFF1E3A8A)],
      verses: [
        VedicVerse(
          sanskrit: 'ॐ त्र्यम्बकं यजामहे सुगन्धिं पुष्टिवर्धनम्।\nउर्वारुकमिव बन्धनान्मृत्योर्मुक्षीय माऽमृतात्॥',
          englishMeaning: 'We worship the Three-Eyed Lord Shiva, who is fragrant and sustains all vitality. As a ripe cucumber easily detaches from its stem, may He liberate us from death and bondages unto immortality.',
          hindiMeaning: 'हम त्रिनेत्र भगवान शिव की पूजा करते हैं, जो सुगंधित हैं और जीवन को पोषण प्रदान करते हैं। जैसे पका हुआ फल डाली से मुक्त हो जाता है, वैसे ही हमें मृत्यु और बंधनों से अमरता की ओर मुक्त करें।',
        ),
      ],
      fullTtsScript: 'ॐ त्र्यम्बकं यजामहे सुगन्धिं पुष्टिवर्धनम्। उर्वारुकमिव बन्धनान्मृत्योर्मुक्षीय माऽमृतात्।',
    ),
    VedicTrack(
      id: 'gayatri_mantra',
      title: 'Gayatri Mantra (108x Divine Chanting)',
      hindiTitle: 'गायत्री मंत्र (ब्रह्म तेजस)',
      category: 'Vedic Illumination Mantra',
      deity: 'Maa Savitri & Surya Dev',
      coverEmoji: '☀️',
      gradientColors: [Color(0xFFC2410C), Color(0xFFF59E0B)],
      verses: [
        VedicVerse(
          sanskrit: 'ॐ भूर्भुवः स्वः तत्सवितुर्वरेण्यं\nभर्गो देवस्य धीमहि धियो यो नः प्रचोदयात्॥',
          englishMeaning: 'We meditate upon the supreme, most adorable effulgence of the Divine Creator Sun. May that radiant cosmic consciousness awaken and enlighten our intellect.',
          hindiMeaning: 'हम उस प्राणस्वरूप, दुःखनाशक, सुखस्वरूप, श्रेष्ठ, तेजस्वी, पापनाशक, देवस्वरूप परमात्मा का ध्यान करते हैं। वह परमात्मा हमारी बुद्धि को सन्मार्ग की ओर प्रेरित करे।',
        ),
      ],
      fullTtsScript: 'ॐ भूर्भुवः स्वः तत्सवितुर्वरेण्यं भर्गो देवस्य धीमहि धियो यो नः प्रचोदयात्।',
    ),
    VedicTrack(
      id: 'shiv_tandav',
      title: 'Shiv Tandav Stotram',
      hindiTitle: 'शिव तांडव स्तोत्रम् (रावण कृत)',
      category: 'Cosmic Shiva Hymn',
      deity: 'Lord Shiva (Nataraja)',
      coverEmoji: '🌙',
      gradientColors: [Color(0xFF1E1B4B), Color(0xFF4338CA)],
      verses: [
        VedicVerse(
          sanskrit: 'जटाटवीगलज्जलप्रवाहपावितस्थले\nगलेऽवलम्ब्य लम्बितां भुजङ्गतुङ्गमालिकाम्।\nडमड्डमड्डमड्डमन्निनादवड्डमर्वयं\nचकार चण्डताण्डवं तनोतु नः शिवः शिवम्॥',
          englishMeaning: 'From His forest of matted locks flows the sanctifying waters of the holy Ganga. Around His neck hangs the great serpent like a sacred garland. As His damaru sounds damad-damad, Lord Shiva performs His cosmic ecstatic dance. May He bestow auspiciousness upon us!',
          hindiMeaning: 'जिनके जटा रूपी वन से बहती गंगा का जल कंठ को पवित्र करता है, जिनके गले में सर्पों का हार है, जो डमरू बजाते हुए प्रचंड तांडव करते हैं, वे भगवान शिव हमारा कल्याण करें।',
        ),
        VedicVerse(
          sanskrit: 'जटाकटाहसम्भ्रमभ्रमन्निलिम्पनिर्झरी-\nविलोलवीचिवल्लरीविराजमानमूर्धनि।\nधगद्धगद्धगज्ज्वलल्ललाटपट्टपावके\nकिशोरचन्द्रशेखरे रतिः प्रतिक्षणं मम॥',
          englishMeaning: 'His forehead blazes with the fire of cosmic dissolution (dhagad-dhagad), while the crescent moon gently gleams on His crown amidst dancing river waves. May my deep devotion remain forever anchored in Lord Chandrashekhara!',
          hindiMeaning: 'जिनके मस्तक पर गंगा की लहरें तरंगित हैं और जिनके ललाट पर अग्नि धधक रही है, उन चंद्रमौलि भगवान शिव में मेरा अनुराग निरंतर बना रहे।',
        ),
      ],
      fullTtsScript: 'जटाटवीगलज्जलप्रवाहपावितस्थले गलेऽवलम्ब्य लम्बितां भुजङ्गतुङ्गमालिकाम्। डमड्डमड्डमड्डमन्निनादवड्डमर्वयं चकार चण्डताण्डवं तनोतु नः शिवः शिवम्।',
    ),
    VedicTrack(
      id: 'shri_suktam',
      title: 'Shri Suktam (Maha Lakshmi Stotram)',
      hindiTitle: 'ऋग्वैदिक श्री सूक्तम् (लक्ष्मी कृपा)',
      category: 'Vedic Prosperity Hymn',
      deity: 'Maa Mahalakshmi',
      coverEmoji: '🪷',
      gradientColors: [Color(0xFF831843), Color(0xFFDB2777)],
      verses: [
        VedicVerse(
          sanskrit: 'ॐ हिरण्यवर्णां हरिणीं सुवर्णरजतस्रजाम्।\nचन्द्रां हिरण्मयीं लक्ष्मीं जातवेदो म आवह॥',
          englishMeaning: 'O Agni (Jatavedas)! Invoke for me Goddess Lakshmi, who is golden-hued, lustrous like a deer, adorned with garlands of gold and silver, and radiant like the soothing moon.',
          hindiMeaning: 'हे अग्निदेव! सुवर्ण के समान वर्ण वाली, सोने और चांदी के आभूषण धारण करने वाली तथा चंद्रमा के समान कांतिमान भगवती महालक्ष्मी का मेरे घर में आह्वान करें।',
        ),
        VedicVerse(
          sanskrit: 'तां म आवह जातवेदो लक्ष्मीमनपगामिनीम्।\nयस्यां हिरण्यं विन्देयं गामश्वं पुरुषानहम्॥',
          englishMeaning: 'O Agni! Summon for me that unwavering Goddess Lakshmi, by whose presence I may attain true wealth, cows, horses, and virtuous companions.',
          hindiMeaning: 'हे जातवेदा! उस नित्य स्थिर रहने वाली लक्ष्मी को बुलाइए, जिनके अनुग्रह से मुझे धन-धान्य, पशुधन और परिवार की समृद्धि प्राप्त हो।',
        ),
      ],
      fullTtsScript: 'ॐ हिरण्यवर्णां हरिणीं सुवर्णरजतस्रजाम्। चन्द्रां हिरण्मयीं लक्ष्मीं जातवेदो म आवह। तां म आवह जातवेदो लक्ष्मीमनपगामिनीम्। यस्यां हिरण्यं विन्देयं गामश्वं पुरुषानहम्।',
    ),
    VedicTrack(
      id: 'om_jai_jagdish',
      title: 'Universal Evening Aarti: Om Jai Jagdish',
      hindiTitle: 'आरती ॐ जय जगदीश हरे (संध्या आरती)',
      category: 'Sacred Temple Aarti',
      deity: 'Lord Maha Vishnu',
      coverEmoji: '🪔',
      gradientColors: [Color(0xFF78350F), Color(0xFFD97706)],
      verses: [
        VedicVerse(
          sanskrit: 'ॐ जय जगदीश हरे, स्वामी जय जगदीश हरे।\nभक्त जनों के संकट, क्षण में दूर करे॥',
          englishMeaning: 'Glory to You, O Lord of the Universe! Who in a single instant dissolves all troubles and tribulations of Your loving devotees.',
          hindiMeaning: 'हे त्रिभुवन के स्वामी श्री जगदीश, आपकी जय हो। आप क्षण भर में अपने भक्तों के संकटों को दूर करते हैं।',
        ),
        VedicVerse(
          sanskrit: 'जो ध्यावै फल पावै, दुख बिनसे मन का।\nसुख सम्पत्ति घर आवै, कष्ट मिटै तन का॥',
          englishMeaning: 'Whoever meditates on You receives auspicious fruits; sorrow departs from the mind. Happiness and abundance grace their home, and physical suffering vanishes.',
          hindiMeaning: 'जो आपका ध्यान करता है, उसके मन का संताप मिट जाता है और घर में सुख-समृद्धि का वास होता है।',
        ),
      ],
      fullTtsScript: 'ॐ जय जगदीश हरे, स्वामी जय जगदीश हरे। भक्त जनों के संकट, क्षण में दूर करे। जो ध्यावै फल पावै, दुख बिनसे मन का। सुख सम्पत्ति घर आवै, कष्ट मिटै तन का।',
    ),
  ];

  @override
  void initState() {
    super.initState();
    _currentTrack = tracks.firstWhere(
      (t) => t.id == widget.initialTrackId,
      orElse: () => tracks.first,
    );
    _initTts();
  }

  @override
  void dispose() {
    _verseTimer?.cancel();
    _tts.stop();
    _verseScroll.dispose();
    super.dispose();
  }

  Future<void> _initTts() async {
    try {
      await _tts.setLanguage("hi-IN");
      await _tts.setSpeechRate(0.36);
      await _tts.setPitch(0.92);
      _tts.setCompletionHandler(() {
        if (!mounted) return;
        if (_isPlaying) {
          setState(() {
            _completedLoops++;
            if (_completedLoops < _loopTarget) {
              _playNextLoop();
            } else {
              _isPlaying = false;
            }
          });
        }
      });
    } catch (_) {}
  }

  void _selectTrack(VedicTrack track) {
    if (_isPlaying) {
      _stopPlayback();
    }
    setState(() {
      _currentTrack = track;
      _activeVerseIndex = 0;
      _completedLoops = 0;
    });
  }

  Future<void> _togglePlay() async {
    if (_isPlaying) {
      await _stopPlayback();
    } else {
      await _startPlayback();
    }
  }

  Future<void> _startPlayback() async {
    setState(() => _isPlaying = true);
    HapticFeedback.mediumImpact();
    await _playNextLoop();

    // Auto-advance verse highlight periodically
    _verseTimer?.cancel();
    _verseTimer = Timer.periodic(const Duration(seconds: 14), (t) {
      if (!mounted || !_isPlaying) {
        t.cancel();
        return;
      }
      setState(() {
        if (_currentTrack.verses.length > 1) {
          _activeVerseIndex = (_activeVerseIndex + 1) % _currentTrack.verses.length;
        }
      });
    });
  }

  Future<void> _playNextLoop() async {
    try {
      await _tts.speak(_currentTrack.fullTtsScript);
    } catch (_) {}
  }

  Future<void> _stopPlayback() async {
    _verseTimer?.cancel();
    await _tts.stop();
    if (mounted) setState(() => _isPlaying = false);
  }

  void _shareTrack() async {
    final t = _currentTrack;
    final buf = StringBuffer();
    buf.writeln('🕉️ *Vedic Chanting Sanctum: ${t.title}* (${t.hindiTitle})');
    buf.writeln('🪔 *Deity:* ${t.deity}');
    buf.writeln('✨ *Sacred Verse:*');
    buf.writeln(t.verses.first.sanskrit);
    buf.writeln('\n🧘 _Listen to authentic Vedic Chants & Aartis on Online Puja:_ https://onlinepuja.live');
    final url = Uri.parse('https://api.whatsapp.com/send?text=${Uri.encodeComponent(buf.toString())}');
    await launchUrl(url, mode: LaunchMode.externalApplication);
  }

  @override
  Widget build(BuildContext context) {
    final isDark = Theme.of(context).brightness == Brightness.dark;
    final t = _currentTrack;

    return Scaffold(
      appBar: AppBar(
        title: const Text('Vedic Audio Sanctum'),
        actions: [
          IconButton(
            tooltip: 'Share Sacred Track',
            icon: const Icon(Icons.share_outlined),
            onPressed: _shareTrack,
          ),
          IconButton(
            tooltip: 'Japa Mala Sadhana',
            icon: const Icon(Icons.circle_outlined),
            onPressed: () => Navigator.of(context).pushNamed(JapaMalaScreen.route),
          ),
        ],
      ),
      body: Container(
        decoration: BoxDecoration(
          gradient: LinearGradient(
            colors: isDark
                ? [const Color(0xFF0F0B08), const Color(0xFF1F1610)]
                : [const Color(0xFFFFFBEB), const Color(0xFFFAF7F2)],
            begin: Alignment.topCenter,
            end: Alignment.bottomCenter,
          ),
        ),
        child: Column(
          children: [
            // Track Selector Carousel Bar
            SizedBox(
              height: 48,
              child: ListView.builder(
                scrollDirection: Axis.horizontal,
                padding: const EdgeInsets.symmetric(horizontal: 12),
                itemCount: tracks.length,
                itemBuilder: (ctx, idx) {
                  final item = tracks[idx];
                  final isSelected = item.id == t.id;
                  return Padding(
                    padding: const EdgeInsets.symmetric(horizontal: 4, vertical: 6),
                    child: ChoiceChip(
                      selected: isSelected,
                      label: Text('${item.coverEmoji} ${item.title}'),
                      labelStyle: TextStyle(
                        fontSize: 11.5,
                        fontWeight: isSelected ? FontWeight.w800 : FontWeight.w500,
                        color: isSelected ? Colors.white : (isDark ? Colors.white70 : Colors.black87),
                      ),
                      selectedColor: const Color(0xFFD97706),
                      backgroundColor: isDark ? Colors.white10 : Colors.white,
                      onSelected: (_) => _selectTrack(item),
                    ),
                  );
                },
              ),
            ),

            Expanded(
              child: ListView(
                controller: _verseScroll,
                padding: const EdgeInsets.all(16),
                children: [
                  // Main Album Visualizer Card
                  Container(
                    padding: const EdgeInsets.all(22),
                    decoration: BoxDecoration(
                      gradient: LinearGradient(
                        colors: t.gradientColors,
                        begin: Alignment.topLeft,
                        end: Alignment.bottomRight,
                      ),
                      borderRadius: BorderRadius.circular(24),
                      boxShadow: [
                        BoxShadow(
                          color: t.gradientColors.first.withValues(alpha: 0.35),
                          blurRadius: 20,
                          offset: const Offset(0, 8),
                        ),
                      ],
                    ),
                    child: Column(
                      children: [
                        Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            Container(
                              padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                              decoration: BoxDecoration(
                                color: Colors.white.withValues(alpha: 0.2),
                                borderRadius: BorderRadius.circular(12),
                              ),
                              child: Text(
                                t.category.toUpperCase(),
                                style: const TextStyle(
                                  color: Colors.white,
                                  fontSize: 10,
                                  fontWeight: FontWeight.w800,
                                  letterSpacing: 0.6,
                                ),
                              ),
                            ),
                            Container(
                              padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                              decoration: BoxDecoration(
                                color: Colors.black26,
                                borderRadius: BorderRadius.circular(12),
                              ),
                              child: Row(
                                mainAxisSize: MainAxisSize.min,
                                children: [
                                  const Icon(Icons.repeat_rounded, color: Colors.white70, size: 14),
                                  const SizedBox(width: 4),
                                  Text(
                                    '$_completedLoops / $_loopTarget Loops',
                                    style: const TextStyle(color: Colors.white, fontSize: 11, fontWeight: FontWeight.bold),
                                  ),
                                ],
                              ),
                            ),
                          ],
                        ),
                        const SizedBox(height: 18),
                        Text(
                          t.coverEmoji,
                          style: const TextStyle(fontSize: 52),
                        ),
                        const SizedBox(height: 12),
                        Text(
                          t.title,
                          textAlign: TextAlign.center,
                          style: const TextStyle(
                            color: Colors.white,
                            fontSize: 22,
                            fontWeight: FontWeight.w900,
                            letterSpacing: -0.3,
                          ),
                        ),
                        const SizedBox(height: 4),
                        Text(
                          t.hindiTitle,
                          style: const TextStyle(
                            color: Colors.white70,
                            fontSize: 14,
                            fontWeight: FontWeight.w600,
                          ),
                        ),
                        const SizedBox(height: 6),
                        Text(
                          'Dedicated to ${t.deity}',
                          style: const TextStyle(
                            color: Colors.white60,
                            fontSize: 11.5,
                            fontStyle: FontStyle.italic,
                          ),
                        ),
                        const SizedBox(height: 20),

                        // Play / Pause Master Button
                        Row(
                          mainAxisAlignment: MainAxisAlignment.center,
                          children: [
                            IconButton(
                              icon: const Icon(Icons.replay_10_rounded, color: Colors.white70, size: 28),
                              onPressed: () {
                                setState(() {
                                  _completedLoops = 0;
                                  _activeVerseIndex = 0;
                                });
                              },
                            ),
                            const SizedBox(width: 16),
                            InkWell(
                              onTap: _togglePlay,
                              borderRadius: BorderRadius.circular(40),
                              child: Container(
                                width: 72,
                                height: 72,
                                decoration: BoxDecoration(
                                  color: Colors.white,
                                  shape: BoxShape.circle,
                                  boxShadow: [
                                    BoxShadow(
                                      color: Colors.black.withValues(alpha: 0.25),
                                      blurRadius: 16,
                                      offset: const Offset(0, 6),
                                    ),
                                  ],
                                ),
                                child: Icon(
                                  _isPlaying ? Icons.pause_rounded : Icons.play_arrow_rounded,
                                  color: t.gradientColors.first,
                                  size: 42,
                                ),
                              ),
                            ),
                            const SizedBox(width: 16),
                            IconButton(
                              icon: const Icon(Icons.skip_next_rounded, color: Colors.white70, size: 28),
                              onPressed: () {
                                final nextIdx = (tracks.indexOf(t) + 1) % tracks.length;
                                _selectTrack(tracks[nextIdx]);
                              },
                            ),
                          ],
                        ),
                      ],
                    ),
                  ),

                  const SizedBox(height: 18),

                  // Loop Target Selector
                  Row(
                    children: [
                      const Icon(Icons.all_inclusive_rounded, color: Color(0xFFD97706), size: 18),
                      const SizedBox(width: 8),
                      const Text(
                        'Target Chanting Loops:',
                        style: TextStyle(fontSize: 12, fontWeight: FontWeight.bold),
                      ),
                      const Spacer(),
                      for (final count in [11, 21, 51, 108])
                        Padding(
                          padding: const EdgeInsets.only(left: 6),
                          child: InkWell(
                            onTap: () {
                              setState(() => _loopTarget = count);
                              HapticFeedback.selectionClick();
                            },
                            borderRadius: BorderRadius.circular(8),
                            child: Container(
                              padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                              decoration: BoxDecoration(
                                color: _loopTarget == count
                                    ? const Color(0xFFD97706)
                                    : (isDark ? Colors.white10 : Colors.black.withValues(alpha: 0.05)),
                                borderRadius: BorderRadius.circular(8),
                              ),
                              child: Text(
                                '${count}x',
                                style: TextStyle(
                                  fontSize: 11,
                                  fontWeight: FontWeight.bold,
                                  color: _loopTarget == count ? Colors.white : null,
                                ),
                              ),
                            ),
                          ),
                        ),
                    ],
                  ),

                  const SizedBox(height: 18),

                  // Sacred Verses & Lyrics Display
                  const Row(
                    children: [
                      Icon(Icons.menu_book_rounded, color: Color(0xFFD97706), size: 18),
                      SizedBox(width: 8),
                      Text(
                        'SACRED SANSKRIT VERSES & MEANING:',
                        style: TextStyle(
                          fontSize: 11,
                          fontWeight: FontWeight.w800,
                          color: Color(0xFFD97706),
                          letterSpacing: 0.8,
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 10),

                  for (var i = 0; i < t.verses.length; i++) ...[
                    Builder(builder: (context) {
                      final v = t.verses[i];
                      final isActive = i == _activeVerseIndex && _isPlaying;

                      return AnimatedContainer(
                        duration: const Duration(milliseconds: 300),
                        margin: const EdgeInsets.only(bottom: 12),
                        padding: const EdgeInsets.all(16),
                        decoration: BoxDecoration(
                          color: isActive
                              ? const Color(0xFFD97706).withValues(alpha: isDark ? 0.22 : 0.12)
                              : (isDark ? const Color(0xFF1E1710) : Colors.white),
                          borderRadius: BorderRadius.circular(16),
                          border: Border.all(
                            color: isActive
                                ? const Color(0xFFD97706)
                                : (isDark ? Colors.white10 : const Color(0xFFE5E7EB)),
                            width: isActive ? 1.8 : 1.0,
                          ),
                          boxShadow: isActive
                              ? [
                                  BoxShadow(
                                    color: const Color(0xFFD97706).withValues(alpha: 0.18),
                                    blurRadius: 12,
                                    offset: const Offset(0, 4),
                                  ),
                                ]
                              : null,
                        ),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.stretch,
                          children: [
                            if (isActive)
                              Row(
                                children: [
                                  Container(
                                    padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
                                    decoration: BoxDecoration(
                                      color: const Color(0xFFD97706),
                                      borderRadius: BorderRadius.circular(6),
                                    ),
                                    child: const Text(
                                      'CHANTING NOW',
                                      style: TextStyle(color: Colors.white, fontSize: 9.5, fontWeight: FontWeight.w900),
                                    ),
                                  ),
                                ],
                              ),
                            if (isActive) const SizedBox(height: 8),
                            Text(
                              v.sanskrit,
                              style: TextStyle(
                                fontSize: 16.5,
                                fontWeight: FontWeight.bold,
                                height: 1.5,
                                color: isActive ? const Color(0xFFD97706) : null,
                              ),
                            ),
                            if (v.hindiMeaning != null) ...[
                              const SizedBox(height: 8),
                              Text(
                                'भावार्थ: ${v.hindiMeaning!}',
                                style: TextStyle(
                                  fontSize: 12.5,
                                  color: isDark ? Colors.white70 : Colors.black87,
                                  height: 1.35,
                                ),
                              ),
                            ],
                            const SizedBox(height: 6),
                            Text(
                              v.englishMeaning,
                              style: TextStyle(
                                fontSize: 11.5,
                                fontStyle: FontStyle.italic,
                                color: isDark ? Colors.white60 : Colors.black54,
                                height: 1.35,
                              ),
                            ),
                          ],
                        ),
                      );
                    }),
                  ],
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}
