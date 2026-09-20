{{-- ══════════ MASTER BRAIN POPUP (floating admin chatbot) ══════════ --}}
<div id="brain-fab" title="Master Brain — AI Growth & Ops Command Center">
    <span class="brain-icon">🧠</span>
    <span class="brain-badge">AI</span>
</div>

<div id="brain-panel" style="display:none;">
    <div id="brain-head">
        <div class="brain-head-title">
            <span class="brain-avatar">🧠</span>
            <div>
                <div class="brain-title-main">MASTER BRAIN</div>
                <div class="brain-subtitle">Autonomous Ops & Growth Engine</div>
            </div>
        </div>
        <span id="brain-close" title="Close">✕</span>
    </div>

    <div id="brain-msgs"></div>

    <div id="brain-chips">
        <span class="bchip" data-q="status report">📊 Status report</span>
        <span class="bchip" data-q="plan cluster for online puja in india">🪔 Plan cluster: Online Puja</span>
        <span class="bchip" data-q="plan cluster for pandit booking online">🧑‍🳒 Plan cluster: Pandit booking</span>
        <span class="bchip" data-q="schedule upcoming hindu festival wishes this month">🙏 Festival wishes this month</span>
        <span class="bchip" data-q="run seo audit">🔍 SEO audit</span>
        <span class="bchip" data-q="write article for ganesh chaturthi puja at home">✍️ Write: Ganesh Chaturthi puja</span>
    </div>

    <form id="brain-form">
        <input id="brain-input" type="text" placeholder="Give a command… e.g. 'status report' or 'plan cluster for havan'" autocomplete="off">
        <button type="submit" id="brain-send-btn" title="Send">➤</button>
    </form>
</div>

<style>
#brain-fab {
    position: fixed;
    right: 26px;
    bottom: 26px;
    z-index: 99998;
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, #ff7a18 0%, #af002d 100%);
    color: #fff;
    font-size: 28px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 8px 24px rgba(175, 0, 45, 0.45), 0 0 0 0 rgba(255, 122, 24, 0.7);
    border: 2px solid rgba(255, 255, 255, 0.85);
    user-select: none;
    transition: transform 0.25s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.25s ease;
    animation: brainPulse 2.5s infinite;
}
#brain-fab:hover {
    transform: scale(1.1);
    box-shadow: 0 12px 30px rgba(175, 0, 45, 0.6);
}
@keyframes brainPulse {
    0% { box-shadow: 0 8px 24px rgba(175, 0, 45, 0.45), 0 0 0 0 rgba(255, 122, 24, 0.7); }
    70% { box-shadow: 0 8px 24px rgba(175, 0, 45, 0.45), 0 0 0 14px rgba(255, 122, 24, 0); }
    100% { box-shadow: 0 8px 24px rgba(175, 0, 45, 0.45), 0 0 0 0 rgba(255, 122, 24, 0); }
}
.brain-badge {
    position: absolute;
    top: -3px;
    right: -3px;
    background: #10b981;
    color: #fff;
    font-size: 10px;
    font-weight: 800;
    padding: 2px 5px;
    border-radius: 10px;
    border: 2px solid #fff;
    letter-spacing: 0.5px;
}

#brain-panel {
    position: fixed;
    right: 26px;
    bottom: 96px;
    z-index: 99999;
    width: 440px;
    max-width: 92vw;
    height: 590px;
    max-height: 82vh;
    background: #ffffff;
    border-radius: 20px;
    box-shadow: 0 16px 50px rgba(0, 0, 0, 0.25), 0 2px 8px rgba(0,0,0,0.08);
    display: none;
    flex-direction: column;
    overflow: hidden;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    border: 1px solid rgba(0, 0, 0, 0.08);
    animation: brainSlideIn 0.25s cubic-bezier(0.16, 1, 0.3, 1);
}
@keyframes brainSlideIn {
    from { opacity: 0; transform: translateY(20px) scale(0.96); }
    to { opacity: 1; transform: translateY(0) scale(1); }
}

#brain-head {
    background: linear-gradient(135deg, #af002d 0%, #ff7a18 100%);
    color: #fff;
    padding: 14px 18px;
    font-weight: 600;
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 14px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}
.brain-head-title {
    display: flex;
    align-items: center;
    gap: 10px;
}
.brain-avatar {
    font-size: 24px;
    background: rgba(255, 255, 255, 0.2);
    width: 38px;
    height: 38px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}
.brain-title-main {
    font-size: 14px;
    font-weight: 700;
    letter-spacing: 0.6px;
    line-height: 1.2;
}
.brain-subtitle {
    font-size: 11px;
    opacity: 0.9;
    font-weight: 400;
}
#brain-close {
    cursor: pointer;
    opacity: 0.9;
    font-size: 18px;
    width: 28px;
    height: 28px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: background 0.2s ease;
}
#brain-close:hover {
    background: rgba(255, 255, 255, 0.25);
    opacity: 1;
}

#brain-msgs {
    flex: 1;
    overflow-y: auto;
    padding: 16px;
    background: #f8fafc;
    font-size: 13.5px;
    line-height: 1.6;
    display: flex;
    flex-direction: column;
    gap: 12px;
}
#brain-msgs::-webkit-scrollbar {
    width: 5px;
}
#brain-msgs::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}

.bmsg {
    padding: 10px 14px;
    border-radius: 14px;
    max-width: 90%;
    white-space: pre-wrap;
    word-wrap: break-word;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
}
.bmsg.user {
    background: #eff6ff;
    color: #1e3a8a;
    margin-left: auto;
    border: 1px solid #bfdbfe;
    border-bottom-right-radius: 4px;
}
.bmsg.brain {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    color: #1e293b;
    margin-right: auto;
    border-bottom-left-radius: 4px;
}
.bmsg.note {
    background: #fefce8;
    border: 1px dashed #facc15;
    color: #854d0e;
    font-size: 12px;
    margin-right: auto;
}
.bmsg.thinking {
    background: #ffffff;
    border: 1px dashed #cbd5e1;
    color: #64748b;
    font-style: italic;
}

.bchip {
    background: #f1f5f9;
    color: #475569;
    font-size: 11.5px;
    padding: 6px 11px;
    border-radius: 16px;
    cursor: pointer;
    border: 1px solid #e2e8f0;
    transition: all 0.18s ease;
    white-space: nowrap;
}
.bchip:hover {
    background: #fff0eb;
    color: #af002d;
    border-color: #ffd6cc;
    transform: translateY(-1px);
}

#brain-chips {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    padding: 10px 14px;
    background: #ffffff;
    border-top: 1px solid #f1f5f9;
    max-height: 95px;
    overflow-y: auto;
}
#brain-chips::-webkit-scrollbar {
    height: 4px;
}

#brain-form {
    display: flex;
    border-top: 1px solid #e2e8f0;
    background: #ffffff;
    padding: 6px 10px;
    align-items: center;
}
#brain-input {
    flex: 1;
    border: none;
    padding: 10px 12px;
    font-size: 13.5px;
    outline: none;
    font-family: inherit;
    background: transparent;
    color: #0f172a;
}
#brain-input::placeholder {
    color: #94a3b8;
}
#brain-form button {
    border: none;
    background: linear-gradient(135deg, #ff7a18 0%, #af002d 100%);
    color: #fff;
    width: 38px;
    height: 38px;
    border-radius: 10px;
    font-size: 15px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: opacity 0.2s ease, transform 0.15s ease;
}
#brain-form button:hover {
    opacity: 0.92;
    transform: scale(1.04);
}
</style>

<script>
(function () {
    var fab = document.getElementById('brain-fab'),
        panel = document.getElementById('brain-panel'),
        msgs = document.getElementById('brain-msgs'),
        form = document.getElementById('brain-form'),
        input = document.getElementById('brain-input'),
        csrf = '{{ csrf_token() }}',
        chatUrl = '{{ route("brain.chat") }}',
        historyUrl = '{{ route("brain.history") }}',
        open = false, booted = false;

    function addMsg(cls, text) {
        var d = document.createElement('div');
        d.className = 'bmsg ' + cls;
        d.textContent = text;
        msgs.appendChild(d);
        msgs.scrollTop = msgs.scrollHeight;
        return d;
    }

    function send(q) {
        addMsg('user', q);
        var t = addMsg('thinking', '⏳ Master Brain thinking…');

        fetch(chatUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrf,
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin',
            body: JSON.stringify({ message: q })
        })
        .then(function (r) {
            if (!r.ok) throw new Error('HTTP ' + r.status);
            return r.json();
        })
        .then(function (j) {
            t.remove();
            addMsg('brain', j.reply || '(No reply text)');

            var res = j.actions && j.actions[0] ? j.actions[0].result : null;
            if (res && typeof res === 'object') {
                addMsg('note', '📋 Execution Result:\n' + JSON.stringify(res, null, 2).substring(0, 600));
            }
        })
        .catch(function (e) {
            t.remove();
            addMsg('note', '⚠ Command failed: ' + e.message);
        });
    }

    function boot() {
        if (booted) return;
        booted = true;

        fetch(historyUrl, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            credentials: 'same-origin'
        })
        .then(function (r) {
            if (!r.ok) return null;
            return r.json();
        })
        .then(function (j) {
            if (!j || !j.messages || j.messages.length === 0) {
                addMsg('note', '🧠 Master Brain online. Type a command or pick a chip below.');
                return;
            }
            j.messages.forEach(function (m) {
                if (m.role === 'user') {
                    addMsg('user', m.text);
                } else if (m.role === 'system_note') {
                    addMsg('note', m.text);
                } else {
                    addMsg('brain', m.text);
                }
            });
        })
        .catch(function () {
            addMsg('note', '🧠 Master Brain online. Type a command or pick a chip below.');
        });
    }

    if (fab) {
        fab.addEventListener('click', function () {
            open = !open;
            panel.style.display = open ? 'flex' : 'none';
            if (open) {
                boot();
                input.focus();
            }
        });
    }

    var closeBtn = document.getElementById('brain-close');
    if (closeBtn) {
        closeBtn.addEventListener('click', function () {
            open = false;
            panel.style.display = 'none';
        });
    }

    Array.prototype.forEach.call(document.querySelectorAll('.bchip'), function (c) {
        c.addEventListener('click', function () {
            input.value = c.getAttribute('data-q');
            form.dispatchEvent(new Event('submit'));
        });
    });

    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            var q = input.value.trim();
            if (!q) return;
            input.value = '';
            send(q);
        });
    }
})();
</script>
