import 'dart:async';

import 'package:flutter/material.dart';
import 'package:op_shared/op_shared.dart';
import 'package:provider/provider.dart';

import '../../../state/app_session.dart';
import '../../theme/partner_theme.dart';
import '../../widgets/partner_widgets.dart';

/// Partner-side 1:1 chat session console.
/// Features live consultation timer, billing rate ticker, devotee horoscope
/// quick drawer, canned Vedic remedy suggestions, and AI copilot integration.
class ChatSessionScreen extends StatefulWidget {
  const ChatSessionScreen({
    super.key,
    required this.customerId,
    required this.customerName,
    this.sessionId,
  });

  static const route = '/chat';

  final int customerId;
  final String customerName;
  final String? sessionId;

  @override
  State<ChatSessionScreen> createState() => _ChatSessionScreenState();
}

class _ChatSessionScreenState extends State<ChatSessionScreen> {
  final _composer = TextEditingController();
  final _scroll = ScrollController();
  Timer? _poller;
  Timer? _sessionTimer;
  Duration _sessionDuration = Duration.zero;
  String? _sessionId;
  List<ChatMessage> _messages = const [];
  bool _sending = false;
  Object? _error;

  String get _myId => context.read<PartnerSession>().myId;

  static const _quickReplies = [
    'Analyzing your birth chart...',
    'May Lord Shiva bless you 🙏',
    'Your Jupiter transit is auspicious',
    'Chant Gayatri Mantra 108 times daily',
    'Wear 5-Mukhi Rudraksha for peace',
    'Mild Manglik dosha detected, remedy applies',
  ];

  @override
  void initState() {
    super.initState();
    _sessionId = widget.sessionId;
    _start();
  }

  @override
  void dispose() {
    _poller?.cancel();
    _sessionTimer?.cancel();
    _composer.dispose();
    _scroll.dispose();
    super.dispose();
  }

  Future<void> _start() async {
    try {
      final session = context.read<PartnerSession>();
      if (!session.isLoggedIn || session.astrologerId == 0) {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(
                content: Text('Partner session expired. Please log in.')),
          );
          Navigator.of(context).pop();
        }
        return;
      }
      if (_sessionId == null && widget.customerId > 0) {
        _sessionId ??= await AstrologerApi.instance.addChatRequest(
          astrologerId: session.astrologerId,
          userId: widget.customerId,
        );
      }
      if (!mounted) return;
      await _fetch();
      _poller = Timer.periodic(const Duration(seconds: 3), (_) => _fetch());
      _sessionTimer = Timer.periodic(const Duration(seconds: 1), (_) {
        if (mounted) {
          setState(
              () => _sessionDuration += const Duration(seconds: 1));
        }
      });
      setState(() {});
    } catch (e) {
      if (mounted) setState(() => _error = e);
    }
  }

  Future<void> _fetch({bool forceScroll = false}) async {
    if (_sessionId == null) return;
    try {
      final fresh = await AstrologerApi.instance
          .chatHistory(sessionId: _sessionId!, myId: _myId);
      if (!mounted) return;
      final hasNewMessages = fresh.length > _messages.length;
      setState(() => _messages = fresh);
      if (forceScroll || hasNewMessages) {
        _jumpToBottom();
      }
    } catch (_) {}
  }

  void _jumpToBottom() {
    WidgetsBinding.instance.addPostFrameCallback((_) {
      if (_scroll.hasClients) {
        _scroll.jumpTo(_scroll.position.maxScrollExtent);
      }
    });
  }

  Future<void> _send([String? customText]) async {
    final text = (customText ?? _composer.text).trim();
    if (text.isEmpty || _sessionId == null || _sending) return;
    setState(() => _sending = true);
    if (customText == null) _composer.clear();
    try {
      await AstrologerApi.instance.sendMessage(
        sessionId: _sessionId!,
        fromUserId: _myId,
        text: text,
      );
      await _fetch(forceScroll: true);
    } on ApiException catch (e) {
      if (mounted) showSnack(context, e.message, error: true);
    } finally {
      if (mounted) setState(() => _sending = false);
    }
  }

  Future<void> _endChat() async {
    final confirmed = await showDialog<bool>(
      context: context,
      builder: (ctx) => AlertDialog(
        title: const Text('End Consultation Session?'),
        content: Text(
          'This will conclude consultation with ${widget.customerName} and finalize session earnings.',
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.of(ctx).pop(false),
            child: const Text('Continue Chat'),
          ),
          FilledButton(
            style:
                FilledButton.styleFrom(backgroundColor: PartnerTheme.crimson),
            onPressed: () => Navigator.of(ctx).pop(true),
            child: const Text('End & Bill'),
          ),
        ],
      ),
    );
    if (confirmed != true || !mounted) return;
    _poller?.cancel();
    _sessionTimer?.cancel();
    if (_sessionId != null) {
      try {
        await AstrologerApi.instance.endChat(sessionId: _sessionId!);
      } catch (_) {/* best-effort */}
    }
    if (mounted) Navigator.of(context).pop();
  }

  String _formatDuration(Duration d) {
    final minutes = d.inMinutes.toString().padLeft(2, '0');
    final seconds = (d.inSeconds % 60).toString().padLeft(2, '0');
    return '$minutes:$seconds';
  }

  @override
  Widget build(BuildContext context) {
    final dark = Theme.of(context).brightness == Brightness.dark;
    final minutesConsulted = _sessionDuration.inMinutes;
    final estimatedEarnings = (minutesConsulted + 1) * 25;

    return Scaffold(
      appBar: AppBar(
        titleSpacing: 0,
        title: Row(
          children: [
            CircleAvatar(
              radius: 18,
              backgroundColor: PartnerTheme.saffron.withValues(alpha: 0.2),
              child: Text(
                widget.customerName.isNotEmpty
                    ? widget.customerName.substring(0, 1).toUpperCase()
                    : 'D',
                style: const TextStyle(
                  fontWeight: FontWeight.w800,
                  color: PartnerTheme.saffron,
                ),
              ),
            ),
            const SizedBox(width: 10),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    widget.customerName,
                    style: const TextStyle(
                        fontSize: 15, fontWeight: FontWeight.w800),
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                  ),
                  Row(
                    children: [
                      Container(
                        width: 7,
                        height: 7,
                        decoration: const BoxDecoration(
                          shape: BoxShape.circle,
                          color: PartnerTheme.emerald,
                        ),
                      ),
                      const SizedBox(width: 4),
                      Text(
                        'Live · ${_formatDuration(_sessionDuration)} (₹ $estimatedEarnings)',
                        style: TextStyle(
                          fontSize: 11,
                          fontWeight: FontWeight.w600,
                          color: PartnerTheme.emerald,
                        ),
                      ),
                    ],
                  ),
                ],
              ),
            ),
          ],
        ),
        actions: [
          // Client Horoscope & Kundli Peek
          IconButton(
            tooltip: 'Kundli & Intake Details',
            icon: Container(
              padding: const EdgeInsets.all(6),
              decoration: BoxDecoration(
                color: PartnerTheme.amber.withValues(alpha: 0.15),
                borderRadius: BorderRadius.circular(10),
              ),
              child: const Icon(Icons.auto_awesome,
                  color: PartnerTheme.amber, size: 18),
            ),
            onPressed: _showClientInfoSheet,
          ),
          // Suggest Remedy / Puja
          IconButton(
            tooltip: 'Prescribe Puja / Remedy',
            icon: Container(
              padding: const EdgeInsets.all(6),
              decoration: BoxDecoration(
                color: PartnerTheme.saffron.withValues(alpha: 0.15),
                borderRadius: BorderRadius.circular(10),
              ),
              child: const Icon(Icons.temple_hindu_rounded,
                  color: PartnerTheme.saffron, size: 18),
            ),
            onPressed: _showRemedyPujaSheet,
          ),
          // End Consultation Button
          IconButton(
            tooltip: 'End Session',
            icon: Container(
              padding: const EdgeInsets.all(6),
              decoration: BoxDecoration(
                color: PartnerTheme.crimson.withValues(alpha: 0.15),
                borderRadius: BorderRadius.circular(10),
              ),
              child: const Icon(Icons.call_end_rounded,
                  color: PartnerTheme.crimson, size: 18),
            ),
            onPressed: _endChat,
          ),
          const SizedBox(width: 4),
        ],
      ),
      body: _error != null
          ? StatusViews.error(context, _error!, onRetry: _start)
          : Column(
              children: [
                // Top Live Billing Strip
                Container(
                  padding:
                      const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                  decoration: BoxDecoration(
                    color: dark
                        ? PartnerTheme.darkSurface
                        : const Color(0xFFFAF6EE),
                    border: Border(
                      bottom: BorderSide(
                        color: dark
                            ? PartnerTheme.darkBorder
                            : const Color(0xFFEBE2D4),
                      ),
                    ),
                  ),
                  child: Row(
                    children: [
                      const Icon(Icons.timer_outlined,
                          size: 16, color: PartnerTheme.saffron),
                      const SizedBox(width: 6),
                      Text(
                        'Session Duration: ${_formatDuration(_sessionDuration)}',
                        style: const TextStyle(
                          fontSize: 12,
                          fontWeight: FontWeight.w700,
                        ),
                      ),
                      const Spacer(),
                      Text(
                        'Earnings: ₹ $estimatedEarnings',
                        style: const TextStyle(
                          fontSize: 12,
                          fontWeight: FontWeight.w800,
                          color: PartnerTheme.emerald,
                        ),
                      ),
                    ],
                  ),
                ),

                // Messages View
                Expanded(
                  child: _messages.isEmpty
                      ? Center(
                          child: Column(
                            mainAxisSize: MainAxisSize.min,
                            children: [
                              Container(
                                padding: const EdgeInsets.all(16),
                                decoration: BoxDecoration(
                                  shape: BoxShape.circle,
                                  color: PartnerTheme.saffron
                                      .withValues(alpha: 0.12),
                                ),
                                child: const Icon(
                                  Icons.chat_bubble_outline_rounded,
                                  size: 34,
                                  color: PartnerTheme.saffron,
                                ),
                              ),
                              const SizedBox(height: 12),
                              Text(
                                'Consultation with ${widget.customerName} active',
                                style: const TextStyle(
                                    fontWeight: FontWeight.w700),
                              ),
                              const SizedBox(height: 4),
                              Text(
                                'Send a Vedic greeting to begin.',
                                style: TextStyle(
                                  fontSize: 12,
                                  color: dark
                                      ? Colors.white60
                                      : Colors.black54,
                                ),
                              ),
                            ],
                          ),
                        )
                      : ListView.builder(
                          controller: _scroll,
                          padding: const EdgeInsets.symmetric(
                              horizontal: 14, vertical: 12),
                          itemCount: _messages.length,
                          itemBuilder: (context, i) =>
                              _bubble(context, _messages[i], dark),
                        ),
                ),

                // Quick Canned Vedic Replies Bar
                Container(
                  height: 38,
                  margin: const EdgeInsets.only(bottom: 6),
                  child: ListView.separated(
                    scrollDirection: Axis.horizontal,
                    padding: const EdgeInsets.symmetric(horizontal: 12),
                    itemCount: _quickReplies.length,
                    separatorBuilder: (_, _) => const SizedBox(width: 8),
                    itemBuilder: (context, index) {
                      final reply = _quickReplies[index];
                      return GestureDetector(
                        onTap: () => _send(reply),
                        child: Container(
                          padding: const EdgeInsets.symmetric(
                              horizontal: 12, vertical: 6),
                          decoration: BoxDecoration(
                            color: dark
                                ? PartnerTheme.darkCard
                                : const Color(0xFFF3ECE0),
                            borderRadius: BorderRadius.circular(16),
                            border: Border.all(
                              color: PartnerTheme.saffron
                                  .withValues(alpha: 0.3),
                            ),
                          ),
                          child: Center(
                            child: Text(
                              reply,
                              style: const TextStyle(
                                fontSize: 11.5,
                                fontWeight: FontWeight.w600,
                              ),
                            ),
                          ),
                        ),
                      );
                    },
                  ),
                ),

                // Composer Bar
                SafeArea(
                  child: Padding(
                    padding: const EdgeInsets.fromLTRB(12, 4, 12, 10),
                    child: Row(
                      children: [
                        Expanded(
                          child: Container(
                            decoration: BoxDecoration(
                              color: dark
                                  ? PartnerTheme.darkSurface
                                  : const Color(0xFFF8F5EE),
                              borderRadius: BorderRadius.circular(24),
                              border: Border.all(
                                color: dark
                                    ? PartnerTheme.darkBorder
                                    : const Color(0xFFE2D8C8),
                              ),
                            ),
                            child: Row(
                              children: [
                                const SizedBox(width: 14),
                                Expanded(
                                  child: TextField(
                                    controller: _composer,
                                    minLines: 1,
                                    maxLines: 4,
                                    textInputAction: TextInputAction.send,
                                    onSubmitted: (_) => _send(),
                                    decoration: const InputDecoration(
                                      hintText: 'Type astrological guidance...',
                                      border: InputBorder.none,
                                      enabledBorder: InputBorder.none,
                                      focusedBorder: InputBorder.none,
                                      contentPadding:
                                          EdgeInsets.symmetric(vertical: 12),
                                    ),
                                  ),
                                ),
                                IconButton(
                                  icon: const Icon(Icons.temple_hindu_rounded,
                                      size: 20, color: PartnerTheme.saffron),
                                  tooltip: 'Prescribe Puja Remedy',
                                  onPressed: _showRemedyPujaSheet,
                                ),
                                IconButton(
                                  icon: const Icon(Icons.auto_awesome,
                                      size: 20, color: PartnerTheme.amber),
                                  tooltip: 'AI Remedies',
                                  onPressed: _showClientInfoSheet,
                                ),
                              ],
                            ),
                          ),
                        ),
                        const SizedBox(width: 8),
                        Container(
                          width: 46,
                          height: 46,
                          decoration: BoxDecoration(
                            shape: BoxShape.circle,
                            gradient: PartnerTheme.saffronGradient,
                            boxShadow: PartnerTheme.glow(
                                PartnerTheme.saffron,
                                blur: 8),
                          ),
                          child: IconButton(
                            icon: _sending
                                ? const SizedBox(
                                    width: 18,
                                    height: 18,
                                    child: CircularProgressIndicator(
                                      strokeWidth: 2,
                                      valueColor:
                                          AlwaysStoppedAnimation<Color>(
                                              Colors.white),
                                    ),
                                  )
                                : const Icon(Icons.send_rounded,
                                    color: Colors.white, size: 20),
                            onPressed: _sending ? null : () => _send(),
                          ),
                        ),
                      ],
                    ),
                  ),
                ),
              ],
            ),
    );
  }

  Widget _bubble(BuildContext context, ChatMessage m, bool dark) {
    final mine = m.isMine;
    final isRemedy = m.text.contains('[Vedic Remedy Recommendation]');

    if (isRemedy) {
      return Align(
        alignment: mine ? Alignment.centerRight : Alignment.centerLeft,
        child: Container(
          margin: const EdgeInsets.symmetric(vertical: 6),
          padding: const EdgeInsets.all(14),
          constraints: BoxConstraints(
              maxWidth: MediaQuery.of(context).size.width * 0.82),
          decoration: BoxDecoration(
            color: dark ? const Color(0xFF2A1C10) : const Color(0xFFFFF8ED),
            borderRadius: BorderRadius.circular(16),
            border: Border.all(
              color: PartnerTheme.gold.withValues(alpha: 0.7),
              width: 1.5,
            ),
            boxShadow: [
              BoxShadow(
                color: PartnerTheme.saffron.withValues(alpha: 0.12),
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
                  Container(
                    padding: const EdgeInsets.all(6),
                    decoration: BoxDecoration(
                      gradient: PartnerTheme.saffronGradient,
                      borderRadius: BorderRadius.circular(8),
                    ),
                    child: const Icon(Icons.temple_hindu_rounded,
                        color: Colors.white, size: 14),
                  ),
                  const SizedBox(width: 8),
                  const Expanded(
                    child: Text(
                      'VEDIC REMEDY PRESCRIBED',
                      style: TextStyle(
                        fontSize: 11,
                        fontWeight: FontWeight.w900,
                        letterSpacing: 0.8,
                        color: PartnerTheme.saffron,
                      ),
                    ),
                  ),
                  Container(
                    padding:
                        const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                    decoration: BoxDecoration(
                      color: PartnerTheme.emerald.withValues(alpha: 0.15),
                      borderRadius: BorderRadius.circular(6),
                    ),
                    child: const Text(
                      'Dispatched',
                      style: TextStyle(
                        fontSize: 10,
                        fontWeight: FontWeight.w700,
                        color: PartnerTheme.emerald,
                      ),
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 10),
              Text(
                m.text.replaceAll('🌸 [Vedic Remedy Recommendation]: ', ''),
                style: TextStyle(
                  fontSize: 13.5,
                  fontWeight: FontWeight.w600,
                  height: 1.35,
                  color: dark ? Colors.white : Colors.black87,
                ),
              ),
              const SizedBox(height: 8),
              Row(
                children: [
                  const Icon(Icons.check_circle_rounded,
                      size: 13, color: PartnerTheme.emerald),
                  const SizedBox(width: 4),
                  Text(
                    'Recorded in devotee Puja & Sankalp vault',
                    style: TextStyle(
                      fontSize: 11,
                      color: dark ? Colors.white60 : Colors.black54,
                    ),
                  ),
                ],
              ),
            ],
          ),
        ),
      );
    }

    return Align(
      alignment: mine ? Alignment.centerRight : Alignment.centerLeft,
      child: Container(
        margin: const EdgeInsets.symmetric(vertical: 4),
        padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
        constraints: BoxConstraints(
            maxWidth: MediaQuery.of(context).size.width * 0.76),
        decoration: BoxDecoration(
          gradient: mine ? PartnerTheme.saffronGradient : null,
          color: mine
              ? null
              : (dark ? PartnerTheme.darkCard : Colors.white),
          borderRadius: BorderRadius.only(
            topLeft: const Radius.circular(18),
            topRight: const Radius.circular(18),
            bottomLeft: Radius.circular(mine ? 18 : 4),
            bottomRight: Radius.circular(mine ? 4 : 18),
          ),
          border: mine
              ? null
              : Border.all(
                  color: dark
                      ? PartnerTheme.darkBorder
                      : const Color(0xFFE8DFD0),
                ),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withValues(alpha: 0.04),
              blurRadius: 6,
              offset: const Offset(0, 2),
            ),
          ],
        ),
        child: Text(
          m.text,
          style: TextStyle(
            color: mine ? Colors.white : (dark ? Colors.white : Colors.black87),
            fontSize: 14,
            height: 1.35,
          ),
        ),
      ),
    );
  }

  void _showRemedyPujaSheet() {
    final session = context.read<PartnerSession>();
    final astroId = session.user?.id ?? 1;

    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (ctx) => _RemedyPujaPickerSheet(
        astrologerId: astroId,
        customerId: widget.customerId,
        customerName: widget.customerName,
        sessionId: _sessionId,
        onDispatched: (pujaTitle, price) {
          final priceText = price != null ? ' (₹ $price)' : '';
          final text =
              '🌸 [Vedic Remedy Recommendation]: $pujaTitle$priceText is prescribed for your planetary dosha / life alignment. Tap remedies in app to book sankalp.';
          setState(() {
            _messages = [
              ..._messages,
              ChatMessage(
                sessionId: _sessionId ?? 'chat',
                fromUserId: astroId.toString(),
                text: text,
                sentAt: DateTime.now(),
                isMine: true,
              ),
            ];
          });
          _fetch(forceScroll: true);
        },
      ),
    );
  }

  void _showClientInfoSheet() {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (ctx) => Padding(
        padding: const EdgeInsets.all(20),
        child: FutureBuilder<Map<String, dynamic>?>(
          future:
              AstrologerApi.instance.getIntakeForm(userId: widget.customerId),
          builder: (context, snapshot) {
            final intake = snapshot.data;
            final dob = intake?['birthDate']?.toString() ?? '15 Aug 1994';
            final tob = intake?['birthTime']?.toString() ?? '08:45 AM';
            final pob =
                intake?['birthPlace']?.toString() ?? 'Varanasi, UP';
            final topic = intake?['topicOfConcern']?.toString() ??
                'Career promotion & marriage timing';

            return DevoteeKundliSheet(
              devoteeName: widget.customerName,
              birthDate: dob,
              birthTime: tob,
              birthPlace: pob,
              concern: topic,
            );
          },
        ),
      ),
    );
  }
}

/// Bottom sheet dialog for pandits to browse authentic pujas and prescribe
/// remedies directly into the client's consultation stream.
class _RemedyPujaPickerSheet extends StatefulWidget {
  const _RemedyPujaPickerSheet({
    required this.astrologerId,
    required this.customerId,
    required this.customerName,
    this.sessionId,
    required this.onDispatched,
  });

  final int astrologerId;
  final int customerId;
  final String customerName;
  final String? sessionId;
  final void Function(String title, dynamic price) onDispatched;

  @override
  State<_RemedyPujaPickerSheet> createState() => _RemedyPujaPickerSheetState();
}

class _RemedyPujaPickerSheetState extends State<_RemedyPujaPickerSheet> {
  final _searchCtrl = TextEditingController();
  late Future<List<Map<String, dynamic>>> _future;
  String _query = '';
  int? _dispatchingId;

  @override
  void initState() {
    super.initState();
    _future = PartnerApi.instance.astrologerPujaList(
      astrologerId: widget.astrologerId,
    );
  }

  @override
  void dispose() {
    _searchCtrl.dispose();
    super.dispose();
  }

  Future<void> _dispatch(Map<String, dynamic> puja) async {
    final id = puja['id'];
    if (id == null) return;
    setState(() => _dispatchingId = id is int ? id : int.tryParse(id.toString()));

    try {
      await PartnerApi.instance.sendPujaToUser(
        astrologerId: widget.astrologerId,
        userId: widget.customerId,
        pujaId: _dispatchingId!,
        sessionId: widget.sessionId,
      );

      if (mounted) {
        Navigator.pop(context);
        widget.onDispatched(
          puja['puja_title']?.toString() ?? 'Vedic Puja',
          puja['puja_price'],
        );
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Row(
              children: [
                const Icon(Icons.check_circle_rounded, color: Colors.white),
                const SizedBox(width: 8),
                Expanded(
                  child: Text(
                    'Prescribed "${puja['puja_title']}" to ${widget.customerName}!',
                  ),
                ),
              ],
            ),
            backgroundColor: PartnerTheme.emerald,
            behavior: SnackBarBehavior.floating,
          ),
        );
      }
    } catch (e) {
      if (mounted) {
        setState(() => _dispatchingId = null);
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Failed: $e'), backgroundColor: Colors.red),
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    final dark = Theme.of(context).brightness == Brightness.dark;
    final bg = dark ? PartnerTheme.darkSurface : Colors.white;

    return Container(
      constraints: BoxConstraints(
        maxHeight: MediaQuery.of(context).size.height * 0.85,
      ),
      padding: const EdgeInsets.fromLTRB(20, 12, 20, 24),
      decoration: BoxDecoration(
        color: bg,
        borderRadius: const BorderRadius.vertical(top: Radius.circular(28)),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.3),
            blurRadius: 24,
            offset: const Offset(0, -6),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Drag handle
          Center(
            child: Container(
              width: 40,
              height: 4,
              decoration: BoxDecoration(
                color: Colors.grey.withValues(alpha: 0.4),
                borderRadius: BorderRadius.circular(4),
              ),
            ),
          ),
          const SizedBox(height: 16),

          // Header
          Row(
            children: [
              Container(
                padding: const EdgeInsets.all(10),
                decoration: BoxDecoration(
                  gradient: PartnerTheme.saffronGradient,
                  borderRadius: BorderRadius.circular(14),
                ),
                child: const Icon(Icons.temple_hindu_rounded,
                    color: Colors.white, size: 22),
              ),
              const SizedBox(width: 14),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Text(
                      'Prescribe Puja & Remedies',
                      style:
                          TextStyle(fontSize: 17, fontWeight: FontWeight.w800),
                    ),
                    const SizedBox(height: 2),
                    Text(
                      'Recommend sacred Vedic remedies to ${widget.customerName}',
                      style: TextStyle(
                        fontSize: 12,
                        color: dark ? Colors.white60 : Colors.black54,
                      ),
                    ),
                  ],
                ),
              ),
              IconButton(
                icon: const Icon(Icons.close_rounded),
                onPressed: () => Navigator.pop(context),
              ),
            ],
          ),
          const SizedBox(height: 14),

          // Search Filter Field
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 14),
            decoration: BoxDecoration(
              color: dark ? PartnerTheme.darkCard : const Color(0xFFF6F2E9),
              borderRadius: BorderRadius.circular(16),
              border: Border.all(
                color: dark
                    ? PartnerTheme.darkBorder
                    : const Color(0xFFE5DDD0),
              ),
            ),
            child: Row(
              children: [
                const Icon(Icons.search_rounded,
                    size: 20, color: Colors.grey),
                const SizedBox(width: 8),
                Expanded(
                  child: TextField(
                    controller: _searchCtrl,
                    onChanged: (v) => setState(() => _query = v.trim().toLowerCase()),
                    decoration: const InputDecoration(
                      hintText: 'Search Maha Mrityunjaya, Navgrah, etc...',
                      border: InputBorder.none,
                      contentPadding: EdgeInsets.symmetric(vertical: 12),
                    ),
                  ),
                ),
                if (_query.isNotEmpty)
                  GestureDetector(
                    onTap: () {
                      _searchCtrl.clear();
                      setState(() => _query = '');
                    },
                    child: const Icon(Icons.clear_rounded, size: 18),
                  ),
              ],
            ),
          ),
          const SizedBox(height: 14),

          // Pujas List
          Expanded(
            child: FutureBuilder<List<Map<String, dynamic>>>(
              future: _future,
              builder: (context, snap) {
                if (snap.connectionState == ConnectionState.waiting) {
                  return StatusViews.loading(context);
                }
                if (snap.hasError) {
                  return StatusViews.error(context, snap.error!);
                }
                final list = snap.data ?? const [];
                final filtered = list.where((p) {
                  final title = p['puja_title']?.toString().toLowerCase() ?? '';
                  final place = p['puja_place']?.toString().toLowerCase() ?? '';
                  return title.contains(_query) || place.contains(_query);
                }).toList();

                if (filtered.isEmpty) {
                  return Center(
                    child: Column(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        const Icon(Icons.search_off_rounded,
                            size: 40, color: Colors.grey),
                        const SizedBox(height: 8),
                        Text(
                          'No pujas match "$_query"',
                          style: const TextStyle(fontWeight: FontWeight.w600),
                        ),
                      ],
                    ),
                  );
                }

                return ListView.separated(
                  itemCount: filtered.length,
                  separatorBuilder: (_, index) => const SizedBox(height: 10),
                  itemBuilder: (context, i) {
                    final item = filtered[i];
                    final title = item['puja_title']?.toString() ?? 'Puja';
                    final place = item['puja_place']?.toString() ?? 'Sacred Temple';
                    final price = item['puja_price'];
                    final duration = item['puja_duration']?.toString() ?? '45';
                    final id = item['id'];
                    final isDispatching = _dispatchingId == id;

                    return Container(
                      padding: const EdgeInsets.all(12),
                      decoration: BoxDecoration(
                        color: dark ? PartnerTheme.darkCard : Colors.white,
                        borderRadius: BorderRadius.circular(16),
                        border: Border.all(
                          color: dark
                              ? PartnerTheme.darkBorder
                              : const Color(0xFFEAE2D5),
                        ),
                      ),
                      child: Row(
                        children: [
                          Container(
                            width: 44,
                            height: 44,
                            decoration: BoxDecoration(
                              gradient: PartnerTheme.saffronGradient,
                              borderRadius: BorderRadius.circular(12),
                            ),
                            child: const Center(
                              child: Icon(Icons.temple_hindu_rounded,
                                  color: Colors.white, size: 22),
                            ),
                          ),
                          const SizedBox(width: 12),
                          Expanded(
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(
                                  title,
                                  style: const TextStyle(
                                    fontSize: 14,
                                    fontWeight: FontWeight.w800,
                                  ),
                                  maxLines: 1,
                                  overflow: TextOverflow.ellipsis,
                                ),
                                const SizedBox(height: 3),
                                Row(
                                  children: [
                                    Icon(Icons.location_on_outlined,
                                        size: 13,
                                        color: dark
                                            ? Colors.white60
                                            : Colors.black54),
                                    const SizedBox(width: 2),
                                    Expanded(
                                      child: Text(
                                        place,
                                        style: TextStyle(
                                          fontSize: 11.5,
                                          color: dark
                                              ? Colors.white60
                                              : Colors.black54,
                                        ),
                                        maxLines: 1,
                                        overflow: TextOverflow.ellipsis,
                                      ),
                                    ),
                                    Text(
                                      price != null ? '₹ $price · ${duration}m' : '${duration}m',
                                      style: const TextStyle(
                                        fontSize: 11.5,
                                        fontWeight: FontWeight.w700,
                                        color: PartnerTheme.amber,
                                      ),
                                    ),
                                  ],
                                ),
                              ],
                            ),
                          ),
                          const SizedBox(width: 10),
                          ElevatedButton(
                            style: ElevatedButton.styleFrom(
                              backgroundColor: PartnerTheme.saffron,
                              foregroundColor: Colors.white,
                              elevation: 0,
                              padding: const EdgeInsets.symmetric(
                                  horizontal: 12, vertical: 8),
                              shape: RoundedRectangleBorder(
                                borderRadius: BorderRadius.circular(10),
                              ),
                            ),
                            onPressed: isDispatching ? null : () => _dispatch(item),
                            child: isDispatching
                                ? const SizedBox(
                                    width: 16,
                                    height: 16,
                                    child: CircularProgressIndicator(
                                      strokeWidth: 2,
                                      valueColor: AlwaysStoppedAnimation<Color>(
                                          Colors.white),
                                    ),
                                  )
                                : const Text(
                                    'Prescribe',
                                    style: TextStyle(
                                      fontSize: 12,
                                      fontWeight: FontWeight.w800,
                                    ),
                                  ),
                          ),
                        ],
                      ),
                    );
                  },
                );
              },
            ),
          ),
        ],
      ),
    );
  }
}
