let autoRefreshInterval = null;

        async function loadRequests() {
            const astrologerId = document.getElementById('astrologerId').value;
            
            if (!astrologerId) {
                alert('Please enter your Astrologer ID');
                return;
            }

            try {
                const response = await fetch('/api/get-pending-requests', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ astrologer_id: astrologerId })
                });

                const data = await response.json();

                if (data.success) {
                    displayRequests(data.requests);
                    
                    // Start auto-refresh
                    if (!autoRefreshInterval) {
                        autoRefreshInterval = setInterval(() => loadRequests(), 3000);
                    }
                }
            } catch (error) {
                console.error('Error:', error);
            }
        }

        function displayRequests(requests) {
            const container = document.getElementById('requestsList');
            
            if (requests.length === 0) {
                container.innerHTML = '<div class="no-requests">No pending requests at the moment</div>';
                return;
            }

            container.innerHTML = requests.map(req => `
                <div class="request-card">
                    <div class="request-info">
                        <h3>📱 Call from ${req.user_name}</h3>
                        <p>👤 User ID: ${req.userId}</p>
                        <p>📧 ${req.user_email}</p>
                        <p>🕐 ${new Date(req.created_at).toLocaleString()}</p>
                        <span class="call-type">${req.call_type === '10' ? '🎙️ Audio Call' : '📹 Video Call'}</span>
                    </div>
                    <div class="request-actions">
                        <button class="btn btn-accept" onclick="acceptRequest(${req.id})">✓ Accept</button>
                        <button class="btn btn-reject" onclick="rejectRequest(${req.id})">✗ Reject</button>
                    </div>
                </div>
            `).join('');
        }

        async function acceptRequest(requestId) {
            const astrologerId = document.getElementById('astrologerId').value;

            try {
                const response = await fetch('/api/accept-call-request', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        request_id: requestId,
                        astrologer_id: astrologerId
                    })
                });

                const data = await response.json();

                if (data.success) {
                    // Redirect to call page
                    window.location.href = `/call/join?requestId=${requestId}&userId=${astrologerId}&userType=astrologer&token=${data.astrologer_token}&channel=${data.channel_name}&callType=${data.call_type}`;
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Failed to accept call');
            }
        }

        async function rejectRequest(requestId) {
            if (!confirm('Are you sure you want to reject this call?')) {
                return;
            }

            try {
                const response = await fetch('/api/reject-call-request', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ request_id: requestId })
                });

                const data = await response.json();

                if (data.success) {
                    loadRequests(); // Refresh list
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Failed to reject call');
            }
        }