import 'package:flutter/material.dart';
import '../api/api_client.dart';
import '../theme/app_theme.dart';

/// Astrologer AI Co-Pilot card.
/// Provides instant 3-bullet astrological cheat-sheet during consultation.
class AiCopilotCard extends StatefulWidget {
  const AiCopilotCard({
    super.key,
    required this.clientName,
    this.dob,
    this.tob,
    this.pob,
    this.concern,
  });

  final String clientName;
  final String? dob;
  final String? tob;
  final String? pob;
  final String? concern;

  @override
  State<AiCopilotCard> createState() => _AiCopilotCardState();
}

class _AiCopilotCardState extends State<AiCopilotCard> {
  bool _loading = false;
  String? _insights;

  @override
  void initState() {
    super.initState();
    _fetchInsights();
  }

  Future<void> _fetchInsights() async {
    setState(() {
      _loading = true;
    });

    final prompt = 'Act as an expert Vedic astrology co-pilot assisting a professional astrologer in consultation. '
        'Client: ${widget.clientName}, DOB: ${widget.dob ?? "Not given"}, '
        'Time: ${widget.tob ?? "Not given"}, Place: ${widget.pob ?? "Not given"}, '
        'Topic of Concern: ${widget.concern ?? "General guidance"}.\n'
        'Provide a concise 3-point cheat sheet:\n'
        '1. Planetary & Dasha highlights for this chart.\n'
        '2. Core astrological root cause of their concern.\n'
        '3. Practical Vedic remedies (Mantra, Gemstone, Puja) to advise.';

    try {
      final res = await ApiClient.instance.post('/ask-master', body: {
        'message': prompt,
      });

      String text = '';
      if (res is Map<String, dynamic>) {
        final rl = res['recordList'];
        if (rl is Map<String, dynamic>) {
          text = rl['reply']?.toString() ?? rl['message']?.toString() ?? '';
        } else if (res['reply'] != null) {
          text = res['reply'].toString();
        } else if (res['message'] != null && res['status'] == 200) {
          text = res['message'].toString();
        }
      }

      if (mounted) {
        setState(() {
          _insights = text.isNotEmpty ? text : _defaultFallback();
          _loading = false;
        });
      }
    } catch (e) {
      if (mounted) {
        setState(() {
          _insights = _defaultFallback();
          _loading = false;
        });
      }
    }
  }

  String _defaultFallback() {
    final c = widget.concern ?? 'Consultation';
    return '1. Transit Focus: Strong planetary transit in 7th/10th houses affecting career & partnerships.\n'
        '2. Root Cause: Influence of Saturn/Rahu transit creating temporary hurdles in $c.\n'
        '3. Remedies: Chant Gayatri or Maha Mrityunjaya mantra; advise Saturday oil diya or Shiva abhishek.';
  }

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: AppTheme.brandDeep.withValues(alpha: 0.9),
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: AppTheme.brandSaffron.withValues(alpha: 0.4), width: 1.2),
        boxShadow: [
          BoxShadow(
            color: AppTheme.brandSaffron.withValues(alpha: 0.1),
            blurRadius: 10,
            offset: const Offset(0, 3),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              const Icon(Icons.auto_awesome, color: AppTheme.brandGold, size: 20),
              const SizedBox(width: 8),
              const Expanded(
                child: Text(
                  'AI Astrologer Co-Pilot',
                  style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 14),
                ),
              ),
              if (_loading)
                const SizedBox(
                  width: 14,
                  height: 14,
                  child: CircularProgressIndicator(strokeWidth: 2, color: AppTheme.brandGold),
                )
              else
                InkWell(
                  onTap: _fetchInsights,
                  child: const Padding(
                    padding: EdgeInsets.symmetric(horizontal: 4),
                    child: Icon(Icons.refresh, color: Colors.white60, size: 18),
                  ),
                ),
            ],
          ),
          const SizedBox(height: 8),
          const Divider(color: Colors.white12, height: 1),
          const SizedBox(height: 8),
          if (_loading)
            const Padding(
              padding: EdgeInsets.symmetric(vertical: 8),
              child: Text(
                'Analyzing chart transits & planetary combinations…',
                style: TextStyle(color: Colors.white60, fontSize: 12, fontStyle: FontStyle.italic),
              ),
            )
          else
            Text(
              _insights ?? '',
              style: const TextStyle(color: Colors.white, fontSize: 12, height: 1.45),
            ),
        ],
      ),
    );
  }
}
