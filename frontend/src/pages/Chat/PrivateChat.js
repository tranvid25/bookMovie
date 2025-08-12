import React, { useEffect, useState, useRef } from "react";
import axios from "axios";
import Echo from "laravel-echo";
import { TOKEN } from "../../util/settings/config";

const PrivateChat = ({ roomId, roomName }) => {
    const [messages, setMessages] = useState([]);
    const [text, setText] = useState("");
    const [file, setFile] = useState(null);
    const [isCalling, setIsCalling] = useState(false);
    const [localStream, setLocalStream] = useState(null);
    const [remoteStream, setRemoteStream] = useState(null);
    const [pc, setPc] = useState(null);
    const chatEndRef = useRef(null);
    const localVideoRef = useRef(null);
    const remoteVideoRef = useRef(null);

    const [userId, setUserId] = useState(Number(localStorage.getItem("userId")) || 0);
    const [accessToken, setAccessToken] = useState(localStorage.getItem(TOKEN));

    useEffect(() => {
        const syncAuth = () => {
            setUserId(Number(localStorage.getItem("userId")) || 0);
            setAccessToken(localStorage.getItem(TOKEN));
        };
        window.addEventListener("storage", syncAuth);
        return () => window.removeEventListener("storage", syncAuth);
    }, []);

    // Lấy lịch sử tin nhắn
    useEffect(() => {
        if (!accessToken || !roomId) return;

        axios
            .get(`http://localhost:8000/api/auth/private-messages/${roomId}`, {
                headers: { Authorization: `Bearer ${accessToken}` },
            })
            .then((res) => {
                if (res.data && res.data.messages) {
                    setMessages(
                        res.data.messages.map((m) => ({
                            id: m.id,
                            from_id: m.user_id,
                            name: m.user?.name || "Ẩn danh",
                            text: m.content,
                            created_at: m.created_at,
                            file_url:m.file_url,
                            file_goc:m.file_goc
                        }))
                    );
                }
            })
            .catch((err) => {
                console.error("Lỗi lấy tin nhắn private:", err);
            });
    }, [accessToken, roomId]);

    // Thiết lập Echo và WebRTC
    useEffect(() => {
        if (!userId || !accessToken || !roomId) return;

        // Khởi tạo Echo
        const echo = new Echo({
            broadcaster: "reverb",
            key: "local",
            wsHost: "localhost",
            wsPort: 8080,
            forceTLS: false,
            auth: { headers: { Authorization: `Bearer ${accessToken}` } }
        });

        // Lắng nghe tin nhắn
        const channel = echo.private(`room.${roomId}`);
        channel.listen("MessagePosted", (data) => {
            setMessages(prev => [...prev, {
                ...data.message,
                name: data.message.user?.name || "Ẩn danh",
                isFile: !!data.message.file_path
            }]);
        });

        // Lắng nghe voice call
        const voiceChannel = echo.private(`voice.${roomId}`);
        voiceChannel.listen("VoiceCall", (data) => {
            handleVoiceSignal(data);
        });

        return () => {
            echo.leave(`room.${roomId}`);
            echo.leave(`voice.${roomId}`);
            if (pc) pc.close();
            if (localStream) localStream.getTracks().forEach(track => track.stop());
        };
    }, [userId, accessToken, roomId]);

    // WebRTC functions
    const startCall = async () => {
        try {
            setIsCalling(true);
            const stream = await navigator.mediaDevices.getUserMedia({ audio: true, video: true });
            setLocalStream(stream);
            if (localVideoRef.current) localVideoRef.current.srcObject = stream;

            const peerConnection = new RTCPeerConnection({
                iceServers: [{ urls: "stun:stun.l.google.com:19302" }]
            });
            setPc(peerConnection);

            stream.getTracks().forEach(track => peerConnection.addTrack(track, stream));

            peerConnection.onicecandidate = (event) => {
                if (event.candidate) {
                    sendSignal({
                        type: "candidate",
                        signal: event.candidate,
                        room_id: roomId
                    });
                }
            };

            peerConnection.ontrack = (event) => {
                setRemoteStream(event.streams[0]);
                if (remoteVideoRef.current) remoteVideoRef.current.srcObject = event.streams[0];
            };

            const offer = await peerConnection.createOffer();
            await peerConnection.setLocalDescription(offer);
            sendSignal({
                type: "offer",
                signal: offer,
                room_id: roomId
            });
        } catch (err) {
            console.error("Call error:", err);
            endCall();
        }
    };

    const handleVoiceSignal = async (data) => {
        if (!pc) return;

        try {
            if (data.type === "offer") {
                await pc.setRemoteDescription(new RTCSessionDescription(data.signal));
                const answer = await pc.createAnswer();
                await pc.setLocalDescription(answer);
                sendSignal({
                    type: "answer",
                    signal: answer,
                    room_id: roomId
                });
            } else if (data.type === "answer") {
                await pc.setRemoteDescription(new RTCSessionDescription(data.signal));
            } else if (data.type === "candidate") {
                await pc.addIceCandidate(new RTCIceCandidate(data.signal));
            }
        } catch (err) {
            console.error("Signal error:", err);
        }
    };

    const sendSignal = (data) => {
        axios.post("http://localhost:8000/api/auth/video-call", data, {
            headers: { Authorization: `Bearer ${accessToken}` }
        }).catch(console.error);
    };

    const endCall = () => {
        if (pc) pc.close();
        if (localStream) localStream.getTracks().forEach(track => track.stop());
        setLocalStream(null);
        setRemoteStream(null);
        setPc(null);
        setIsCalling(false);
    };

    // Gửi tin nhắn
     const sendPrivateMessage = async () => {
        if (!text.trim() && !file) return;

        try {
            console.log("📤 Đang gửi tin nhắn đến room:", roomId);

            const formData = new FormData();
            formData.append("content", text);
            formData.append("room_id", roomId);

            if (file) {
                formData.append("file", file);
            }

            const response = await axios.post("http://localhost:8000/api/auth/messagePrivate", formData, {
                headers: {
                    Authorization: `Bearer ${accessToken}`,
                    "Content-Type": "multipart/form-data",
                },
            });

            console.log("✅ Gửi tin nhắn thành công:", response.data);
            
            // Thêm tin nhắn vào state ngay lập tức
            const newMessage = {
                id: response.data.message.id,
                from_id: response.data.message.user_id,
                name: response.data.message.user?.name || "Ẩn danh",
                text: response.data.message.content,
                created_at: response.data.message.created_at,
            };
            
            setMessages(prev => [...prev, newMessage]);
            setText("");
            setFile(null);
        } catch (err) {
            console.error("❌ Gửi tin nhắn private thất bại:", err);
        }
    };

    // Render tin nhắn
     const renderMessage = (msg) => {
        const isUser = msg.from_id === userId;
        const isFile = msg.file_url;
        const isImage = isFile && /\.(jpg|jpeg|png|gif)$/i.test(msg.file_url);

        return (
            <div key={msg.id} style={{
                display: "flex",
                justifyContent: isUser ? "flex-end" : "flex-start",
                marginBottom: 8,
                padding: "0 16px"
            }}>
                <div style={{
                    maxWidth: "70%",
                    backgroundColor: isUser ? "#0084ff" : "#e4e6eb",
                    color: isUser ? "#fff" : "#050505",
                    borderRadius: isUser ? "18px 18px 0 18px" : "18px 18px 18px 0",
                    padding: "8px 12px",
                    wordBreak: "break-word",
                    boxShadow: "0 1px 2px rgba(0,0,0,0.1)",
                    position: "relative"
                }}>
                    {/* Hiển thị tên nếu không phải là tin nhắn của bạn */}
                    {!isUser && (
                        <div style={{
                            fontSize: 13,
                            fontWeight: "bold",
                            marginBottom: 4,
                            color: "#385898"
                        }}>
                            {msg.name}
                        </div>
                    )}
                    
                    {/* Hiển thị nội dung tin nhắn hoặc file */}
                    {isFile ? (
                        isImage ? (
                            <img 
                                src={msg.file_url} 
                                alt="File" 
                                style={{ 
                                    maxWidth: "100%", 
                                    maxHeight: 300,
                                    borderRadius: 8,
                                    display: "block"
                                }}
                            />
                        ) : (
                            <a 
                                href={msg.file_url} 
                                target="_blank" 
                                rel="noopener noreferrer"
                                style={{ 
                                    color: isUser ? "#fff" : "#0084ff",
                                    textDecoration: "none",
                                    display: "inline-block",
                                    padding: "8px 12px",
                                    backgroundColor: isUser ? "rgba(0,0,0,0.1)" : "rgba(0,0,0,0.05)",
                                    borderRadius: 8
                                }}
                            >
                                📎 {msg.file_goc}
                            </a>
                        )
                    ) : (
                        <div style={{ lineHeight: 1.4 }}>{msg.text}</div>
                    )}
                    
                    {/* Thời gian gửi */}
                    <div style={{
                        fontSize: 11,
                        marginTop: 4,
                        textAlign: "right",
                        color: isUser ? "rgba(255,255,255,0.7)" : "rgba(0,0,0,0.4)"
                    }}>
                        {new Date(msg.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}
                    </div>
                </div>
            </div>
        );
    };

    return (
        <div style={{
            maxWidth: 800,
            margin: "0 auto",
            height: "100vh",
            display: "flex",
            flexDirection: "column",
            backgroundColor: "#f0f2f5"
        }}>
            {/* Header */}
            <div style={{
                display: "flex",
                justifyContent: "space-between",
                alignItems: "center",
                padding: "12px 16px",
                backgroundColor: "#0084ff",
                color: "#fff",
                boxShadow: "0 1px 4px rgba(0,0,0,0.1)"
            }}>
                <h2 style={{ margin: 0, fontSize: 18 }}>{roomName}</h2>
                {!isCalling ? (
                    <button onClick={startCall} style={{
                        padding: "6px 12px",
                        background: "#fff",
                        color: "#0084ff",
                        border: "none",
                        borderRadius: 4,
                        cursor: "pointer",
                        fontWeight: "bold"
                    }}>
                        📞 Gọi
                    </button>
                ) : (
                    <button onClick={endCall} style={{
                        padding: "6px 12px",
                        background: "#ff4d4f",
                        color: "#fff",
                        border: "none",
                        borderRadius: 4,
                        cursor: "pointer",
                        fontWeight: "bold"
                    }}>
                        📞 Kết thúc
                    </button>
                )}
            </div>

            {/* Video call */}
            {isCalling && (
                <div style={{
                    display: "flex",
                    gap: 20,
                    padding: 16,
                    backgroundColor: "#000",
                    justifyContent: "center"
                }}>
                    <div style={{ textAlign: "center" }}>
                        <video 
                            ref={localVideoRef} 
                            autoPlay 
                            muted 
                            style={{ 
                                width: 150, 
                                height: 200,
                                objectFit: "cover",
                                borderRadius: 8
                            }} 
                        />
                        <div style={{ color: "#fff", marginTop: 8 }}>Bạn</div>
                    </div>
                    <div style={{ textAlign: "center" }}>
                        <video 
                            ref={remoteVideoRef} 
                            autoPlay 
                            style={{ 
                                width: 150, 
                                height: 200,
                                objectFit: "cover",
                                borderRadius: 8
                            }} 
                        />
                        <div style={{ color: "#fff", marginTop: 8 }}>Đối phương</div>
                    </div>
                </div>
            )}

            {/* Danh sách tin nhắn */}
            <div style={{
                flex: 1,
                overflowY: "auto",
                padding: "16px 0",
                backgroundImage: "linear-gradient(#f7f7f7, #f0f2f5)"
            }}>
                {messages.length > 0 ? messages.map(renderMessage) : (
                    <div style={{
                        textAlign: "center",
                        color: "#65676b",
                        padding: 40,
                        fontSize: 15
                    }}>
                        <p>Chưa có tin nhắn nào</p>
                        <small>Bắt đầu cuộc trò chuyện bằng cách gửi tin nhắn</small>
                    </div>
                )}
                <div ref={chatEndRef} />
            </div>

            {/* Input gửi tin nhắn */}
            <div style={{
                display: "flex",
                gap: 8,
                padding: 12,
                backgroundColor: "#fff",
                borderTop: "1px solid #ddd",
                alignItems: "center"
            }}>
                <input
                    type="file"
                    id="file-upload"
                    onChange={(e) => setFile(e.target.files[0])}
                    style={{ display: "none" }}
                />
                <label htmlFor="file-upload" style={{
                    padding: "8px 12px",
                    borderRadius: "50%",
                    cursor: "pointer",
                    color: "#65676b",
                    fontSize: 20
                }}>
                    📎
                </label>
                
                <input
                    type="text"
                    value={text}
                    onChange={(e) => setText(e.target.value)}
                    onKeyPress={(e) => e.key === "Enter" && sendPrivateMessage()}
                    placeholder="Nhập tin nhắn..."
                    style={{
                        flex: 1,
                        padding: "10px 16px",
                        borderRadius: 20,
                        border: "none",
                        backgroundColor: "#f0f2f5",
                        outline: "none",
                        fontSize: 15
                    }}
                />
                
                <button
                    onClick={sendPrivateMessage}
                    disabled={!text.trim() && !file}
                    style={{
                        padding: "10px 16px",
                        backgroundColor: "#0084ff",
                        color: "#fff",
                        border: "none",
                        borderRadius: "50%",
                        cursor: "pointer",
                        opacity: (!text.trim() && !file) ? 0.5 : 1,
                        fontSize: 20,
                        display: "flex",
                        alignItems: "center",
                        justifyContent: "center"
                    }}
                >
                    {!text.trim() && !file ? "😊" : "✈️"}
                </button>
            </div>
        </div>
    );
};



export default PrivateChat;