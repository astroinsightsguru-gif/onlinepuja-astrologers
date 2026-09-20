import 'package:flutter/material.dart';
import 'package:op_shared/op_shared.dart';
import 'package:provider/provider.dart';

import '../../../state/app_session.dart';
import 'kundli_detail_screen.dart';

/// Saved kundli list + add-new form (legacy `kundliScreen.dart` /
/// `createNewKundli.dart`).
class KundliListScreen extends StatefulWidget {
  const KundliListScreen({super.key});

  @override
  State<KundliListScreen> createState() => _KundliListScreenState();
}

class _KundliListScreenState extends State<KundliListScreen> {
  List<Kundli>? _items;
  Object? _error;

  @override
  void initState() {
    super.initState();
    _load();
  }

  int get _userId => context.read<AppSession>().user?.id ?? 0;

  Future<void> _load() async {
    setState(() => _error = null);
    try {
      final items = await KundliApi.instance.list(userId: _userId);
      if (mounted) setState(() => _items = items);
    } catch (e) {
      if (mounted) setState(() => _error = e);
    }
  }

  Future<void> _openAdd() async {
    final created = await showModalBottomSheet<bool>(
      context: context,
      isScrollControlled: true,
      builder: (_) => const _AddKundliSheet(),
    );
    if (created == true && mounted) {
      showSnack(context, 'Kundli saved');
      _load();
    }
  }

  Future<void> _delete(Kundli k) async {
    final ok = await showDialog<bool>(
      context: context,
      builder: (ctx) => AlertDialog(
        title: Text('Delete ${k.name}?'),
        content:
            const Text('This saved birth chart will be removed permanently.'),
        actions: [
          TextButton(
              onPressed: () => Navigator.pop(ctx, false),
              child: const Text('Cancel')),
          FilledButton(
              onPressed: () => Navigator.pop(ctx, true),
              child: const Text('Delete')),
        ],
      ),
    );
    if (ok != true) return;
    try {
      await KundliApi.instance.delete(kundaliId: k.id ?? 0);
      if (mounted) {
        showSnack(context, 'Deleted');
        _load();
      }
    } on ApiException catch (e) {
      if (mounted) showSnack(context, e.message, error: true);
    }
  }

  @override
  Widget build(BuildContext context) {
    final scheme = Theme.of(context).colorScheme;
    final loggedIn = _userId != 0;
    return Scaffold(
      appBar: AppBar(title: const Text('Kundli')),
      floatingActionButton: loggedIn
          ? FloatingActionButton.extended(
              onPressed: _openAdd,
              icon: const Icon(Icons.add_rounded),
              label: const Text('New Kundli'),
            )
          : null,
      body: !loggedIn
          ? StatusViews.empty(context,
              message: 'Log in to create and save kundlis')
          : _error != null
              ? StatusViews.error(context, _error!, onRetry: _load)
              : _items == null
                  ? StatusViews.loading(context)
                  : _items!.isEmpty
                      ? StatusViews.empty(context,
                          message: 'No saved kundlis yet.\nTap "New Kundli" '
                              'to add one.')
                      : RefreshIndicator(
                          onRefresh: _load,
                          child: ListView.separated(
                            padding: const EdgeInsets.all(16),
                            itemCount: _items!.length,
                            separatorBuilder: (_, _) =>
                                const SizedBox(height: 10),
                            itemBuilder: (context, i) {
                              final k = _items![i];
                              return Card(
                                margin: EdgeInsets.zero,
                                child: ListTile(
                                  leading: CircleAvatar(
                                    backgroundColor: scheme.primaryContainer,
                                    child: Text(
                                      k.name.isNotEmpty
                                          ? k.name[0].toUpperCase()
                                          : '?',
                                      style: TextStyle(
                                          color: scheme.primary,
                                          fontWeight: FontWeight.w800),
                                    ),
                                  ),
                                  title: Text(k.name),
                                  subtitle: Text(
                                    '${k.birthDate.toIso8601String().substring(0, 10)} · '
                                    '${k.birthTime} · ${k.birthPlace}',
                                    maxLines: 1,
                                    overflow: TextOverflow.ellipsis,
                                  ),
                                  trailing: IconButton(
                                    icon: const Icon(Icons.delete_outline),
                                    onPressed: () => _delete(k),
                                  ),
                                  onTap: () => Navigator.of(context).push(
                                    MaterialPageRoute(
                                      builder: (_) =>
                                          KundliDetailScreen(kundli: k),
                                    ),
                                  ),
                                ),
                              );
                            },
                          ),
                        ),
    );
  }
}

/// Bottom-sheet form to create a kundli (legacy `createNewKundli.dart`).
class _AddKundliSheet extends StatefulWidget {
  const _AddKundliSheet();

  @override
  State<_AddKundliSheet> createState() => _AddKundliSheetState();
}

class _AddKundliSheetState extends State<_AddKundliSheet> {
  final _formKey = GlobalKey<FormState>();
  final _name = TextEditingController();
  final _place = TextEditingController();
  final _lat = TextEditingController(text: '28.6139');
  final _lng = TextEditingController(text: '77.2090');
  String _gender = 'Male';
  DateTime _date = DateTime.now();
  TimeOfDay _time = const TimeOfDay(hour: 8, minute: 0);
  bool _saving = false;

  @override
  void dispose() {
    _name.dispose();
    _place.dispose();
    _lat.dispose();
    _lng.dispose();
    super.dispose();
  }

  Future<void> _pickDate() async {
    final picked = await showDatePicker(
      context: context,
      initialDate: _date,
      firstDate: DateTime(1900),
      lastDate: DateTime.now().add(const Duration(days: 1)),
    );
    if (picked != null) setState(() => _date = picked);
  }

  Future<void> _pickTime() async {
    final picked = await showTimePicker(context: context, initialTime: _time);
    if (picked != null) setState(() => _time = picked);
  }

  Future<void> _save() async {
    if (!_formKey.currentState!.validate()) return;
    setState(() => _saving = true);
    final session = context.read<AppSession>();
    String two(int v) => v.toString().padLeft(2, '0');
    final k = Kundli(
      name: _name.text.trim(),
      gender: _gender,
      birthDate: _date,
      birthTime: '${two(_time.hour)}:${two(_time.minute)}',
      birthPlace: _place.text.trim(),
      latitude: double.tryParse(_lat.text.trim()),
      longitude: double.tryParse(_lng.text.trim()),
      timezone: 5.5,
      lang: 'en',
      isActive: 1,
      isDelete: 0,
      createdBy: session.user?.id,
      forMatch: 0,
    );
    try {
      await KundliApi.instance.add(k);
      if (mounted) Navigator.pop(context, true);
    } on ApiException catch (e) {
      if (mounted) showSnack(context, e.message, error: true);
    } catch (e) {
      if (mounted) showSnack(context, e.toString(), error: true);
    } finally {
      if (mounted) setState(() => _saving = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: EdgeInsets.only(bottom: MediaQuery.of(context).viewInsets.bottom),
      child: SingleChildScrollView(
        padding: const EdgeInsets.all(20),
        child: Form(
          key: _formKey,
          child: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              Text('New Kundli',
                  style: Theme.of(context)
                      .textTheme
                      .titleLarge
                      ?.copyWith(fontWeight: FontWeight.w800)),
              const SizedBox(height: 16),
              TextFormField(
                controller: _name,
                decoration: const InputDecoration(
                    labelText: 'Full name',
                    prefixIcon: Icon(Icons.person_outline)),
                validator: (v) =>
                    (v == null || v.trim().isEmpty) ? 'Enter a name' : null,
              ),
              const SizedBox(height: 12),
              DropdownButtonFormField<String>(
                initialValue: _gender,
                decoration: const InputDecoration(
                    labelText: 'Gender', prefixIcon: Icon(Icons.wc_rounded)),
                items: const [
                  DropdownMenuItem(value: 'Male', child: Text('Male')),
                  DropdownMenuItem(value: 'Female', child: Text('Female')),
                  DropdownMenuItem(value: 'Other', child: Text('Other')),
                ],
                onChanged: (v) => setState(() => _gender = v ?? 'Male'),
              ),
              const SizedBox(height: 12),
              TextFormField(
                controller: _place,
                decoration: const InputDecoration(
                    labelText: 'Birth place',
                    prefixIcon: Icon(Icons.location_city_rounded)),
                validator: (v) => (v == null || v.trim().isEmpty)
                    ? 'Enter birth place'
                    : null,
              ),
              const SizedBox(height: 12),
              Row(
                children: [
                  Expanded(
                    child: OutlinedButton.icon(
                      onPressed: _pickDate,
                      icon: const Icon(Icons.calendar_month_rounded),
                      label: Text('${_date.day}/${_date.month}/${_date.year}'),
                    ),
                  ),
                  const SizedBox(width: 10),
                  Expanded(
                    child: OutlinedButton.icon(
                      onPressed: _pickTime,
                      icon: const Icon(Icons.schedule_rounded),
                      label: Text(_time.format(context)),
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 12),
              Row(
                children: [
                  Expanded(
                    child: TextFormField(
                      controller: _lat,
                      keyboardType: const TextInputType.numberWithOptions(
                          decimal: true),
                      decoration: const InputDecoration(labelText: 'Latitude'),
                    ),
                  ),
                  const SizedBox(width: 10),
                  Expanded(
                    child: TextFormField(
                      controller: _lng,
                      keyboardType: const TextInputType.numberWithOptions(
                          decimal: true),
                      decoration:
                          const InputDecoration(labelText: 'Longitude'),
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 20),
              FilledButton(
                style: FilledButton.styleFrom(
                    minimumSize: const Size.fromHeight(50)),
                onPressed: _saving ? null : _save,
                child: _saving
                    ? const SizedBox(
                        width: 20,
                        height: 20,
                        child: CircularProgressIndicator(strokeWidth: 2))
                    : const Text('Save Kundli'),
              ),
              const SizedBox(height: 8),
            ],
          ),
        ),
      ),
    );
  }
}
