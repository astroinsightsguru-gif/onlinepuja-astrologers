import 'package:flutter/material.dart';
import 'package:op_shared/op_shared.dart';

import '../../theme/customer_theme.dart';
import '../japa/japa_mala_screen.dart';
import '../mall/mall_screen.dart';
import '../puja/puja_detail_screen.dart';

/// Represents a classical Vedic Dosha and its three-fold scriptural remedies:
/// 1. Temple Puja & Havanam
/// 2. AstroMall Holy Item (Rudraksha / Gemstone / Yantra)
/// 3. Japa Mala Chanting Sadhana
class DoshaRemedyItem {
  final String id;
  final String name;
  final String hindiName;
  final String sanskritShloka;
  final String diagnosis;
  final String impact;
  final String severity; // 'High', 'Moderate', 'Mild'
  final Color badgeColor;

  // Remedy 1: Temple Puja
  final String pujaTitle;
  final String templeName;
  final String pujaBenefit;
  final double pujaEstimatedDakshina;

  // Remedy 2: AstroMall Holy Item
  final String productTitle;
  final String productType;
  final String productBenefit;

  // Remedy 3: Japa Sadhana
  final String mantraTitle;
  final String mantraSanskrit;
  final String mantraMeaning;
  final int prescribedRepetitions;

  const DoshaRemedyItem({
    required this.id,
    required this.name,
    required this.hindiName,
    required this.sanskritShloka,
    required this.diagnosis,
    required this.impact,
    required this.severity,
    required this.badgeColor,
    required this.pujaTitle,
    required this.templeName,
    required this.pujaBenefit,
    required this.pujaEstimatedDakshina,
    required this.productTitle,
    required this.productType,
    required this.productBenefit,
    required this.mantraTitle,
    required this.mantraSanskrit,
    required this.mantraMeaning,
    this.prescribedRepetitions = 108,
  });

  static const List<DoshaRemedyItem> classicalRemedies = [
    DoshaRemedyItem(
      id: 'manglik',
      name: 'Manglik Dosha (Kuja Dosha)',
      hindiName: 'मांगलिक दोष (कुज दोष)',
      sanskritShloka: 'धने व्यये च पाताले जामित्रे चाष्टमे कुजे। कन्याया भर्तृनाशः स्याद् भर्तुः कन्या विनश्यति॥',
      diagnosis: 'Mars placed in 1st, 4th, 7th, 8th or 12th house in Lagna/Moon chart.',
      impact: 'Creates delays in marriage, friction between spouses, and impulsive temperaments if unpacified.',
      severity: 'High',
      badgeColor: Color(0xFFDC2626),
      pujaTitle: 'Bhauma Mangal Shanti & Kumbh Vivah Mahapuja',
      templeName: 'Mangalnath Mandir, Ujjain (Angarak Kshetra)',
      pujaBenefit: 'Directly pacifies fiery Mars vibrations, dissolves matrimonial friction, and bestows marital longevity.',
      pujaEstimatedDakshina: 1501,
      productTitle: 'Natural Red Coral (त्रिकोण मूंगा) / 3-Mukhi Rudraksha',
      productType: 'Vedic Gemstone / Rudraksha',
      productBenefit: 'Worn on Tuesday in copper or gold; cools Mars aggression and channels vital prana into constructive courage.',
      mantraTitle: 'Mangal Gayatri Mantra',
      mantraSanskrit: 'ॐ क्षितिपुत्राय विद्महे लोहिताङ्गाय धीमहि तन्नो भौमः प्रचोदयात्॥',
      mantraMeaning: 'We meditate upon the son of Mother Earth, radiant in red light. May Lord Bhauma illuminate our intellect.',
      prescribedRepetitions: 108,
    ),
    DoshaRemedyItem(
      id: 'kaalsarp',
      name: 'Kaal Sarp Dosha',
      hindiName: 'कालसर्प योग दोष',
      sanskritShloka: 'अग्रे राहुरधः केतुः सर्वे मध्यगता ग्रहाः। योगाऽयं कालसर्पाख्यो नृपतिं रङ्कतां नयेत्॥',
      diagnosis: 'All seven planets hemmed between the nodal axis of Rahu and Ketu.',
      impact: 'Frequent obstacles in career breakthroughs, sudden reversals, and recurring serpent or anxiety dreams.',
      severity: 'High',
      badgeColor: Color(0xFF7C3AED),
      pujaTitle: 'Trimbakeshwar Kaal Sarp Dosh Nivaran Maha Havanam',
      templeName: 'Shri Trimbakeshwar Jyotirlinga, Nashik',
      pujaBenefit: 'Sacred Narayana Nagbali ritual conducted at the source of Godavari, liberating soul from karmic serpentine bonds.',
      pujaEstimatedDakshina: 2100,
      productTitle: 'Silver Naag-Naagin Pair & Rahu-Ketu Shanti Yantra',
      productType: 'Sacred Silver Yantra & Pratima',
      productBenefit: 'Consecrated during Rahu Kalam; shields aura against psychic intrusions and dispels subconscious fear.',
      mantraTitle: 'Maha Mrityunjaya Mantra',
      mantraSanskrit: 'ॐ त्र्यम्बकं यजामहे सुगन्धिं पुष्टिवर्धनम्। उर्वारुकमिव बन्धनान्मृत्योर्मुक्षीय माऽमृतात्॥',
      mantraMeaning: 'We worship the Three-Eyed Lord Shiva who nourishes all life. Deliver us from death and bondages unto immortality.',
      prescribedRepetitions: 108,
    ),
    DoshaRemedyItem(
      id: 'sadesati',
      name: 'Shani Sade Sati & Dhaiya',
      hindiName: 'शनि साढ़े साती एवं ढैया',
      sanskritShloka: 'नीलाञ्जनसमाभासं रविपुत्रं यमाग्रजम्। छायामार्तण्डसम्भूतं तं नमामि शनैश्चरम्॥',
      diagnosis: 'Saturn transiting 12th, 1st, or 2nd house from natal Janma Rashi (Moon sign).',
      impact: 'Period of profound karmic testing, financial delays, heavy responsibilities, and emotional endurance.',
      severity: 'Moderate',
      badgeColor: Color(0xFF2563EB),
      pujaTitle: 'Shani Shingnapur Telabhishek & Shani Maha Yagya',
      templeName: 'Shri Shani Shingnapur Swayambhu Devasthan, Maharashtra',
      pujaBenefit: 'Black sesame & mustard oil anointing on the Swayambhu Shila softens karmic retributions into spiritual discipline.',
      pujaEstimatedDakshina: 1100,
      productTitle: '7-Mukhi Nepali Rudraksha & Authentic Black Horse-Shoe Ring',
      productType: 'Sacred Rudraksha & Kale Ghode Ki Naal Ring',
      productBenefit: 'Ruled by Goddess Mahalakshmi and Lord Saturn; protects wearers from legal issues, financial drains, and accidents.',
      mantraTitle: 'Shani Beej Mantra',
      mantraSanskrit: 'ॐ प्रां प्रीं प्रौं सः शनैश्चराय नमः॥',
      mantraMeaning: 'Salutations to Lord Shanaishchara, the son of Surya, who balances cosmic justice and dispels negativity.',
      prescribedRepetitions: 108,
    ),
    DoshaRemedyItem(
      id: 'pitra',
      name: 'Pitra Dosha (Ancestral Affliction)',
      hindiName: 'पितृ दोष निवारण',
      sanskritShloka: 'आयुः प्रजां धनं विद्यां स्वर्गं मोक्षं सुखानि च। प्रयच्छन्ति तथा राज्यं प्रीताः नृणां पितामहाः॥',
      diagnosis: 'Sun or Moon afflicted by Rahu/Ketu/Saturn in 9th house of dharma and paternal lineage.',
      impact: 'Delays in progeny, unexplainable sickness in family members, and persistent blockages in lineage prosperity.',
      severity: 'High',
      badgeColor: Color(0xFFB45309),
      pujaTitle: 'Gaya Tirth Pitra Shanti & Tripindi Shradh Mahapuja',
      templeName: 'Vishnupad Mandir & Falgu River, Gaya Ji',
      pujaBenefit: 'Offers sacred Pinda daan at the footprints of Lord Vishnu, releasing ancestral souls into higher heavenly realms.',
      pujaEstimatedDakshina: 2500,
      productTitle: '11-Mukhi Rudraksha (Rudra Swaroop) & Shrimad Bhagavad Gita',
      productType: 'Authentic Nepali Rudraksha',
      productBenefit: 'Invokes the 11 Rudras to cleanse 7 generations of karmic debt and grant divine ancestral blessings.',
      mantraTitle: 'Gayatri Mantra for Pitra Moksha',
      mantraSanskrit: 'ॐ भूर्भुवः स्वः तत्सवितुर्वरेण्यं भर्गो देवस्य धीमहि धियो यो नः प्रचोदयात्॥',
      mantraMeaning: 'We meditate on the divine radiance of the supreme creator Sun. May His light illuminate all generations.',
      prescribedRepetitions: 108,
    ),
    DoshaRemedyItem(
      id: 'nadi',
      name: 'Nadi Dosha (Vivah Compatibility)',
      hindiName: 'नाड़ी दोष निवारण (विवाह मिलान)',
      sanskritShloka: 'एकनाडी विवाहश्च न कर्तव्यो विचक्षणैः। नृणां च जायते नाशः कुलनाशाभिधीयते॥',
      diagnosis: 'Bride and groom share the identical Nadi (Aadi, Madhya, or Antya) in Ashtakoot matching.',
      impact: 'Classical Vedic astrology cautions regarding genetic disharmony, progeny vulnerabilities, and mutual physical vitality.',
      severity: 'High',
      badgeColor: Color(0xFFE11D48),
      pujaTitle: 'Nadi Dosha Shanti Maha Mrityunjaya & Swarna Daan Havanam',
      templeName: 'Kashi Vishwanath Mandir, Varanasi',
      pujaBenefit: 'Performed by 5 Vedic Brahmins with gold and cow donation rituals to nullify genetic and physiological disharmony.',
      pujaEstimatedDakshina: 2100,
      productTitle: 'Gauri Shankar Rudraksha (Sacred Union)',
      productType: 'Naturally Conjoined Rudraksha',
      productBenefit: 'Symbol of Shiva & Parvati; harmonizes energy polarities and protects marital bliss and progeny health.',
      mantraTitle: 'Maha Mrityunjaya Samput Mantra',
      mantraSanskrit: 'ॐ हौं जूं सः ॐ भूर्भुवः स्वः ॐ त्र्यम्बकं यजामहे सुगन्धिं पुष्टिवर्धनम्। उर्वारुकमिव बन्धनान्मृत्योर्मुक्षीय माऽमृतात्॥',
      mantraMeaning: 'Sacred Tantric Mrityunjaya invocation dissolving health afflictions and uniting soulmates in divine health.',
      prescribedRepetitions: 108,
    ),
    DoshaRemedyItem(
      id: 'bhakoot',
      name: 'Bhakoot Dosha (Moon Sign Angle)',
      hindiName: 'भकूट दोष (षडाष्टक / नवम-पंचम)',
      sanskritShloka: 'निधनं षडष्टके ज्ञेयं नवमे पञ्चमेऽपि वा। कन्यायां च कुले नाशः सम्प्राप्ते भकूटदोषतः॥',
      diagnosis: 'Moon signs of couple are in 6-8 (Shadashtaka) or 9-5 (Navam-Pancham) or 12-2 (Dwidwadasha) relationship.',
      impact: 'May trigger emotional misunderstandings, friction in financial planning, or career relocation conflicts.',
      severity: 'Moderate',
      badgeColor: Color(0xFFD97706),
      pujaTitle: 'Gauri Shankar Vivah Samvad & Navagraha Shanti',
      templeName: 'Meenakshi Amman Temple, Madurai',
      pujaBenefit: 'Invokes divine union of Goddess Meenakshi and Lord Sundareswarar to bless the couple with lasting understanding.',
      pujaEstimatedDakshina: 1800,
      productTitle: '2-Mukhi Ardhanarishwar Rudraksha',
      productType: 'Sacred Nepali Rudraksha',
      productBenefit: 'Represents unity of thought between two hearts; balances Moon energies and removes psychological distance.',
      mantraTitle: 'Shiva Parvati Preeti Mantra',
      mantraSanskrit: 'ॐ उमामहेश्वराभ्यां नमः॥',
      mantraMeaning: 'I bow with devotion to the divine eternal couple Goddess Uma and Lord Maheshwara for harmony in domestic life.',
      prescribedRepetitions: 108,
    ),
  ];

  static DoshaRemedyItem? findById(String id) {
    for (final item in classicalRemedies) {
      if (item.id.toLowerCase() == id.toLowerCase()) return item;
    }
    return null;
  }
}

/// Rich Vedic Dosha Remedy Card with 1-Tap actions:
/// 1. Book Temple Puja
/// 2. Purchase Prescribed AstroMall Item
/// 3. Launch 108x Japa Mala Sadhana
class DoshaRemedyCard extends StatelessWidget {
  const DoshaRemedyCard({
    super.key,
    required this.remedy,
    this.compact = false,
  });

  final DoshaRemedyItem remedy;
  final bool compact;

  @override
  Widget build(BuildContext context) {
    final isDark = Theme.of(context).brightness == Brightness.dark;
    final r = remedy;

    return Container(
      margin: const EdgeInsets.only(bottom: 16),
      decoration: BoxDecoration(
        color: isDark ? const Color(0xFF1E1710) : Colors.white,
        borderRadius: BorderRadius.circular(20),
        border: Border.all(
          color: r.badgeColor.withValues(alpha: isDark ? 0.35 : 0.25),
          width: 1.2,
        ),
        boxShadow: [
          BoxShadow(
            color: r.badgeColor.withValues(alpha: 0.08),
            blurRadius: 14,
            offset: const Offset(0, 5),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          // Header Banner
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
            decoration: BoxDecoration(
              color: r.badgeColor.withValues(alpha: isDark ? 0.20 : 0.08),
              borderRadius: const BorderRadius.vertical(top: Radius.circular(19)),
            ),
            child: Row(
              children: [
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                  decoration: BoxDecoration(
                    color: r.badgeColor,
                    borderRadius: BorderRadius.circular(8),
                  ),
                  child: Text(
                    r.severity.toUpperCase(),
                    style: const TextStyle(
                      color: Colors.white,
                      fontSize: 10.5,
                      fontWeight: FontWeight.w900,
                      letterSpacing: 0.5,
                    ),
                  ),
                ),
                const SizedBox(width: 10),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        r.name,
                        style: const TextStyle(
                          fontSize: 15,
                          fontWeight: FontWeight.w800,
                        ),
                      ),
                      Text(
                        r.hindiName,
                        style: TextStyle(
                          fontSize: 12,
                          color: isDark ? Colors.white70 : Colors.black54,
                          fontWeight: FontWeight.w500,
                        ),
                      ),
                    ],
                  ),
                ),
                const Icon(Icons.shield_outlined, color: Color(0xFFD97706), size: 22),
              ],
            ),
          ),

          Padding(
            padding: const EdgeInsets.all(16),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                // Sanskrit Shloka Quote
                Container(
                  padding: const EdgeInsets.all(10),
                  decoration: BoxDecoration(
                    color: const Color(0xFFD97706).withValues(alpha: 0.07),
                    borderRadius: BorderRadius.circular(10),
                    border: Border.all(color: const Color(0xFFD97706).withValues(alpha: 0.2)),
                  ),
                  child: Row(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      const Text('🕉️ ', style: TextStyle(fontSize: 14)),
                      Expanded(
                        child: Text(
                          r.sanskritShloka,
                          style: const TextStyle(
                            fontSize: 11.5,
                            fontStyle: FontStyle.italic,
                            fontWeight: FontWeight.w600,
                            height: 1.35,
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
                const SizedBox(height: 10),

                // Diagnosis & Impact
                Text(
                  r.diagnosis,
                  style: TextStyle(
                    fontSize: 12.5,
                    fontWeight: FontWeight.w600,
                    color: isDark ? Colors.amber.shade200 : const Color(0xFF92400E),
                  ),
                ),
                const SizedBox(height: 4),
                Text(
                  r.impact,
                  style: TextStyle(
                    fontSize: 12,
                    color: isDark ? Colors.white70 : Colors.black87,
                    height: 1.35,
                  ),
                ),

                const Divider(height: 22),

                // Prescribed Vedic Remedies Section
                const Text(
                  'PRESCRIBED 3-FOLD VEDIC REMEDIES (त्रिविध उपाय):',
                  style: TextStyle(
                    fontSize: 10.5,
                    fontWeight: FontWeight.w800,
                    color: Color(0xFFD97706),
                    letterSpacing: 0.8,
                  ),
                ),
                const SizedBox(height: 10),

                // 1. Temple Puja Remedy
                _RemedyPillarTile(
                  icon: Icons.temple_hindu_rounded,
                  iconColor: const Color(0xFFE11D48),
                  title: '1. Temple Puja & Havanam',
                  primaryText: r.pujaTitle,
                  secondaryText: 'Kshetra: ${r.templeName}',
                  actionLabel: 'Book Puja (₹${r.pujaEstimatedDakshina.toInt()})',
                  actionColor: const Color(0xFFE11D48),
                  onAction: () {
                    Navigator.of(context).push(
                      MaterialPageRoute(
                        builder: (_) => PujaDetailScreen(
                          puja: Puja(
                            id: 1,
                            title: r.pujaTitle,
                            place: r.templeName,
                            longDescription: r.pujaBenefit,
                          ),
                        ),
                      ),
                    );
                  },
                ),
                const SizedBox(height: 10),

                // 2. AstroMall Holy Item
                _RemedyPillarTile(
                  icon: Icons.diamond_outlined,
                  iconColor: const Color(0xFF2563EB),
                  title: '2. AstroMall Sacred Item',
                  primaryText: r.productTitle,
                  secondaryText: r.productBenefit,
                  actionLabel: 'Explore in AstroMall',
                  actionColor: const Color(0xFF2563EB),
                  onAction: () {
                    Navigator.of(context).pushNamed(MallScreen.route);
                  },
                ),
                const SizedBox(height: 10),

                // 3. Japa Sadhana Chanting
                _RemedyPillarTile(
                  icon: Icons.record_voice_over_rounded,
                  iconColor: const Color(0xFF059669),
                  title: '3. Daily Japa Mala Sadhana',
                  primaryText: r.mantraTitle,
                  secondaryText: '${r.mantraSanskrit}\n(${r.prescribedRepetitions} repetitions daily)',
                  actionLabel: 'Chant 108x with Mala',
                  actionColor: const Color(0xFF059669),
                  onAction: () {
                    Navigator.of(context).pushNamed(JapaMalaScreen.route);
                  },
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}

class _RemedyPillarTile extends StatelessWidget {
  const _RemedyPillarTile({
    required this.icon,
    required this.iconColor,
    required this.title,
    required this.primaryText,
    required this.secondaryText,
    required this.actionLabel,
    required this.actionColor,
    required this.onAction,
  });

  final IconData icon;
  final Color iconColor;
  final String title;
  final String primaryText;
  final String secondaryText;
  final String actionLabel;
  final Color actionColor;
  final VoidCallback onAction;

  @override
  Widget build(BuildContext context) {
    final isDark = Theme.of(context).brightness == Brightness.dark;

    return Container(
      padding: const EdgeInsets.all(12),
      decoration: BoxDecoration(
        color: isDark ? Colors.black26 : const Color(0xFFF9FAFB),
        borderRadius: BorderRadius.circular(14),
        border: Border.all(
          color: isDark ? Colors.white12 : const Color(0xFFE5E7EB),
        ),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Container(
                padding: const EdgeInsets.all(6),
                decoration: BoxDecoration(
                  color: iconColor.withValues(alpha: 0.12),
                  shape: BoxShape.circle,
                ),
                child: Icon(icon, color: iconColor, size: 16),
              ),
              const SizedBox(width: 8),
              Expanded(
                child: Text(
                  title,
                  style: TextStyle(
                    fontSize: 11.5,
                    fontWeight: FontWeight.w700,
                    color: iconColor,
                  ),
                ),
              ),
            ],
          ),
          const SizedBox(height: 6),
          Text(
            primaryText,
            style: const TextStyle(
              fontSize: 13.5,
              fontWeight: FontWeight.bold,
            ),
          ),
          const SizedBox(height: 3),
          Text(
            secondaryText,
            style: TextStyle(
              fontSize: 11.5,
              color: isDark ? Colors.white60 : Colors.black54,
              height: 1.3,
            ),
          ),
          const SizedBox(height: 8),
          SizedBox(
            width: double.infinity,
            child: OutlinedButton.icon(
              icon: const Icon(Icons.arrow_forward_rounded, size: 14),
              label: Text(actionLabel, style: const TextStyle(fontSize: 11.5, fontWeight: FontWeight.bold)),
              style: OutlinedButton.styleFrom(
                foregroundColor: actionColor,
                side: BorderSide(color: actionColor.withValues(alpha: 0.6)),
                padding: const EdgeInsets.symmetric(vertical: 8),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
              ),
              onPressed: onAction,
            ),
          ),
        ],
      ),
    );
  }
}

/// Comprehensive Dosha Nivaran Hub Sheet
class DoshaRemedySheet extends StatelessWidget {
  const DoshaRemedySheet({super.key, this.highlightedDoshaId});

  final String? highlightedDoshaId;

  static void show(BuildContext context, {String? doshaId}) {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (_) => DoshaRemedySheet(highlightedDoshaId: doshaId),
    );
  }

  @override
  Widget build(BuildContext context) {
    final isDark = Theme.of(context).brightness == Brightness.dark;
    final list = DoshaRemedyItem.classicalRemedies;

    return DraggableScrollableSheet(
      initialChildSize: 0.88,
      minChildSize: 0.5,
      maxChildSize: 0.95,
      builder: (ctx, scroll) => Container(
        decoration: BoxDecoration(
          color: isDark ? const Color(0xFF18130E) : Colors.white,
          borderRadius: const BorderRadius.vertical(top: Radius.circular(24)),
        ),
        child: Column(
          children: [
            Container(
              margin: const EdgeInsets.only(top: 10, bottom: 6),
              width: 44,
              height: 5,
              decoration: BoxDecoration(
                color: Colors.grey.withValues(alpha: 0.4),
                borderRadius: BorderRadius.circular(10),
              ),
            ),
            Padding(
              padding: const EdgeInsets.fromLTRB(20, 8, 20, 12),
              child: Row(
                children: [
                  Container(
                    padding: const EdgeInsets.all(8),
                    decoration: BoxDecoration(
                      gradient: CustomerTheme.saffronGradient,
                      borderRadius: BorderRadius.circular(12),
                    ),
                    child: const Icon(Icons.shield_moon_rounded, color: Colors.white, size: 20),
                  ),
                  const SizedBox(width: 12),
                  const Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          'Dosha Nivaran & Vedic Remedies',
                          style: TextStyle(fontSize: 16, fontWeight: FontWeight.w900),
                        ),
                        Text(
                          'Classical Vedic scripture-anchored pacification rituals',
                          style: TextStyle(fontSize: 11, color: Colors.grey),
                        ),
                      ],
                    ),
                  ),
                  IconButton(
                    icon: const Icon(Icons.close_rounded),
                    onPressed: () => Navigator.pop(ctx),
                  ),
                ],
              ),
            ),
            const Divider(height: 1),
            Expanded(
              child: ListView.builder(
                controller: scroll,
                padding: const EdgeInsets.all(16),
                itemCount: list.length,
                itemBuilder: (ctx, idx) => DoshaRemedyCard(remedy: list[idx]),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
