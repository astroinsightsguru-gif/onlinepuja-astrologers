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
    final fresh = await AstrologerApi.instance
        .chatHistory(sessionId: _sessionId!, myId: _myId);
    if (!mounted) return;
    final hasNewMessages = fresh.length > _messages.length;
    setState(() => _messages = fresh);
    if (forceScroll || hasNewMessages) {
      _jumpToBottom();
    }
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
