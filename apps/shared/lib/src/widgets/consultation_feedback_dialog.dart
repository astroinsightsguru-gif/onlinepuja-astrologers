import 'package:flutter/material.dart';
import '../services/astrologer_api.dart';

/// Shows an interactive post-consultation rating dialog.
Future<void> showConsultationFeedbackDialog({
  required BuildContext context,
  required int astrologerId,
  required String astrologerName,
}) async {
  await showDialog<void>(
    context: context,
    barrierDismissible: true,
    builder: (ctx) => _FeedbackDialog(
      astrologerId: astrologerId,
      astrologerName: astrologerName,
    ),
  );
}

class _FeedbackDialog extends StatefulWidget {
  const _FeedbackDialog({
    required this.astrologerId,
    required this.astrologerName,
  });

  final int astrologerId;
  final String astrologerName;

  @override
  State<_FeedbackDialog> createState() => _FeedbackDialogState();
}

class _FeedbackDialogState extends State<_FeedbackDialog> {
  int _stars = 5;
  final _commentCtrl = TextEditingController();
  final Set<String> _selectedChips = {};
  int _selectedDakshina = 0;
  bool _submitting = false;

  static const _tagOptions = [
    '✨ Very Accurate',
    '🙏 Calming Guidance',
    '🌸 Helpful Remedies',
    '💎 Polite & Patient',
    '⭐ Highly Recommended',
  ];

  @override
  void dispose() {
    _commentCtrl.dispose();
    super.dispose();
  }

  Future<void> _submit() async {
    setState(() => _submitting = true);
    final comment = [
      if (_selectedDakshina > 0) "🌸 Offered Dakshina: ₹$_selectedDakshina",

      ..._selectedChips,
      if (_commentCtrl.text.trim().isNotEmpty) _commentCtrl.text.trim(),
    ].join(' · ');

    try {
      await AstrologerApi.instance.addReview(
        astrologerId: widget.astrologerId,
        rating: _stars.toDouble(),
        review: comment.isEmpty ? 'Consultation completed' : comment,
      );
    } catch (_) {
      // Best-effort rating submission
    }

    if (mounted) {
      Navigator.of(context).pop();
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Thank you for your valuable feedback! 🙏'),
          behavior: SnackBarBehavior.floating,
        ),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);
    final scheme = theme.colorScheme;

    return AlertDialog(
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(24)),
      contentPadding: const EdgeInsets.fromLTRB(20, 24, 20, 16),
      content: SingleChildScrollView(
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Container(
              padding: const EdgeInsets.all(14),
              decoration: BoxDecoration(
                color: Colors.amber.withValues(alpha: 0.15),
                shape: BoxShape.circle,
              ),
              child: const Icon(Icons.star_rounded, color: Colors.amber, size: 38),
            ),
            const SizedBox(height: 14),
            Text(
              'Rate Consultation',
              style: theme.textTheme.titleMedium?.copyWith(fontWeight: FontWeight.bold),
            ),
            const SizedBox(height: 4),
            Text(
              'How was your session with ${widget.astrologerName}?',
              textAlign: TextAlign.center,
              style: theme.textTheme.bodySmall?.copyWith(color: scheme.outline),
            ),
            const SizedBox(height: 16),

            // 5 Star row
            Row(
              mainAxisAlignment: MainAxisAlignment.center,
              children: List.generate(5, (index) {
                final starNum = index + 1;
                return IconButton(
                  iconSize: 34,
                  padding: const EdgeInsets.symmetric(horizontal: 2),
                  constraints: const BoxConstraints(),
                  icon: Icon(
                    starNum <= _stars ? Icons.star_rounded : Icons.star_outline_rounded,
                    color: Colors.amber,
                  ),
                  onPressed: () => setState(() => _stars = starNum),
                );
              }),
            ),
            const SizedBox(height: 16),

            // Quick chips
            Wrap(
              spacing: 6,
              runSpacing: 6,
              alignment: WrapAlignment.center,
              children: _tagOptions.map((tag) {
                final active = _selectedChips.contains(tag);
                return FilterChip(
                  label: Text(tag, style: const TextStyle(fontSize: 11)),
                  selected: active,
                  onSelected: (val) {
                    setState(() {
                      if (val) {
                        _selectedChips.add(tag);
                      } else {
                        _selectedChips.remove(tag);
                      }
                    });
                  },
                );
              }).toList(),
            ),
            const SizedBox(height: 14),

            // Dakshina / Tipping Section (Astrotalk / AstroSage benchmark)
            Container(
              padding: const EdgeInsets.all(12),
              decoration: BoxDecoration(
                color: Colors.amber.shade50,
                borderRadius: BorderRadius.circular(14),
                border: Border.all(color: Colors.amber.shade300),
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    children: const [
                      Icon(Icons.volunteer_activism_rounded, color: Colors.deepOrange, size: 18),
                      SizedBox(width: 6),
                      Text(
                        'Offer Dakshina (दान / दक्षिणा)',
                        style: TextStyle(fontWeight: FontWeight.bold, fontSize: 12.5, color: Colors.deepOrange),
                      ),
                    ],
                  ),
                  const SizedBox(height: 8),
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceEvenly,
                    children: [21, 51, 101, 201].map((amt) {
                      final isSelected = _selectedDakshina == amt;
                      return ChoiceChip(
                        label: Text('₹$amt'),
                        selected: isSelected,
                        selectedColor: Colors.deepOrange,
                        labelStyle: TextStyle(
                          color: isSelected ? Colors.white : Colors.black87,
                          fontWeight: FontWeight.bold,
                          fontSize: 12,
                        ),
                        onSelected: (val) {
                          setState(() => _selectedDakshina = val ? amt : 0);
                        },
                      );
                    }).toList(),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 14),

            // Comment textfield
            TextField(
              controller: _commentCtrl,
              maxLines: 2,
              decoration: InputDecoration(
                hintText: 'Share more about your experience (optional)',
                hintStyle: TextStyle(fontSize: 12, color: scheme.outline),
                border: OutlineInputBorder(borderRadius: BorderRadius.circular(14)),
                contentPadding: const EdgeInsets.all(12),
              ),
            ),
          ],
        ),
      ),
      actions: [
        TextButton(
          onPressed: () => Navigator.of(context).pop(),
          child: const Text('Skip'),
        ),
        FilledButton(
          onPressed: _submitting ? null : _submit,
          child: _submitting
              ? const SizedBox(width: 18, height: 18, child: CircularProgressIndicator(strokeWidth: 2))
              : const Text('Submit Review'),
        ),
      ],
    );
  }
}
