import React, { useEffect, useState, useRef } from "react";
import axios from "axios";
import Echo from "laravel-echo";
import { TOKEN } from "../../util/settings/config";

const PrivateChat = ({ roomId, roomName }) => {
    const [messages, setMessages] = useState([]);
    const [text, setText] = useState("");
    const [file, setFile] = useState(null);
    const chatEndRef = useRef(null);

    // Sử dụng state để lưu userId và accessToken, đồng bộ với localStorage
    const [userId, setUserId] = useState(Number(localStorage.getItem("userId")) || 0);
    const [accessToken, setAccessToken] = useState(localStorage.getItem(TOKEN));

    // Lắng nghe sự thay đổi của localStorage
    useEffect(() => {
        const syncAuth = () => {
            setUserId(Number(localStorage.getItem("userId")) || 0);
            setAccessToken(localStorage.getItem(TOKEN));
        };
        window.addEventListener("storage", syncAuth);
        return () => window.removeEventListener("storage", syncAuth);
    }, []);

    // Lấy lịch sử tin nhắn private khi load trang
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
                        }))
                    );
                }
            })
            .catch((err) => {
                console.error("Lỗi lấy tin nhắn private:", err);
            });
    }, [accessToken, roomId]);

    // Lắng nghe realtime private chat với Laravel Reverb
    useEffect(() => {
        if (!userId || !accessToken || !roomId) return;

        console.log("🚀 Bắt đầu khởi tạo Echo với Reverb...");
        console.log("📋 Thông tin kết nối:", { userId, roomId, accessToken: accessToken ? "Có" : "Không" });

        // Khởi tạo Echo với Reverb
        const echo = new Echo({
            broadcaster: "reverb",
            key: "local",
            wsHost: "localhost",
            wsPort: 8080,
            forceTLS: false,
            enabledTransports: ["ws"],
            disableStats: true,
            timeout: 5000,
            authEndpoint: "http://localhost:8000/broadcasting/auth",
            auth: {
                headers: {
                    Authorization: `Bearer ${accessToken}`,
                },
            },
        });

        // Thêm error handling cho Echo (bỏ theo dõi socket trực tiếp để tránh lỗi)

        // Join private channel cho room cụ thể
        const channel = echo.private(`room.${roomId}`);

        console.log("🔗 Đã kết nối đến channel:", `room.${roomId}`);

        // Lắng nghe kết nối
        channel.subscribed(() => {
            console.log("✅ Đã subscribe thành công channel:", `room.${roomId}`);
        });

        channel.error((error) => {
            console.error("❌ Lỗi kết nối channel:", error);
        });

        // Lắng nghe event MessagePosted từ backend
        channel.listen("MessagePosted", (data) => {
            console.log("📨 Nhận được tin nhắn realtime:", data);
            setMessages((prev) => [
                ...prev,
                {
                    id: data.message.id,
                    from_id: data.message.user_id,
                    name: data.message.user?.name || "Ẩn danh",
                    text: data.message.content,
                    created_at: data.message.created_at,
                },
            ]);
        });

        return () => {
            echo.leave(`room.${roomId}`);
        };
    }, [userId, accessToken, roomId]);

    useEffect(() => {
        chatEndRef.current?.scrollIntoView({ behavior: "smooth" });
    }, [messages]);

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

    return (
        <div
            className="private-chat-container"
            style={{
                maxWidth: 600,
                margin: "20px auto",
                border: "1px solid #ddd",
                borderRadius: 12,
                padding: 20,
                backgroundColor: "#fff",
                boxShadow: "0 4px 6px rgba(0, 0, 0, 0.1)",
            }}
        >
            <div style={{
                textAlign: "center",
                marginBottom: 20,
                padding: "10px 0",
                borderBottom: "2px solid #007bff",
                color: "#007bff"
            }}>
                <h3>💬 {roomName || `Phòng ${roomId}`}</h3>
                <small style={{ color: "#666" }}>Chat riêng tư</small>
            </div>

            <div
                className="chat-messages"
                style={{
                    height: 400,
                    overflowY: "auto",
                    background: "#f8f9fa",
                    padding: 16,
                    marginBottom: 16,
                    borderRadius: 8,
                    border: "1px solid #e9ecef",
                }}
            >
                {messages.length === 0 ? (
                    <div style={{
                        textAlign: "center",
                        color: "#666",
                        marginTop: "50px"
                    }}>
                        <p>Chưa có tin nhắn nào</p>
                        <small>Hãy bắt đầu cuộc trò chuyện!</small>
                    </div>
                ) : (
                    messages.map((msg, idx) => {
                        const isUser = msg.from_id === userId;
                        return (
                            <div
                                key={msg.id || idx}
                                style={{
                                    display: "flex",
                                    justifyContent: isUser ? "flex-end" : "flex-start",
                                    marginBottom: 12,
                                }}
                            >
                                <div
                                    style={{
                                        maxWidth: "70%",
                                        backgroundColor: isUser ? "#007bff" : "#e9ecef",
                                        color: isUser ? "#fff" : "#333",
                                        borderRadius: 18,
                                        padding: "12px 16px",
                                        wordBreak: "break-word",
                                        boxShadow: "0 2px 4px rgba(0,0,0,0.1)",
                                    }}
                                >
                                    <div style={{
                                        fontSize: "12px",
                                        marginBottom: "4px",
                                        opacity: 0.8
                                    }}>
                                        {msg.name}
                                    </div>
                                    <div>{msg.text}</div>
                                    <div style={{
                                        fontSize: "10px",
                                        marginTop: "4px",
                                        opacity: 0.6
                                    }}>
                                        {new Date(msg.created_at).toLocaleTimeString()}
                                    </div>
                                </div>
                            </div>
                        );
                    })
                )}
                <div ref={chatEndRef} />
            </div>

            <div className="chat-input" style={{ display: "flex", gap: 12 }}>
                <input
                    value={text}
                    onChange={(e) => setText(e.target.value)}
                    onKeyDown={(e) => e.key === "Enter" && sendPrivateMessage()}
                    placeholder="Nhập tin nhắn riêng tư..."
                    style={{
                        flex: 1,
                        padding: "12px 16px",
                        borderRadius: 25,
                        border: "1px solid #ddd",
                        fontSize: "14px",
                    }}
                />
                <input
                    type="file"
                    onChange={(e) => setFile(e.target.files[0])}
                    style={{
                        padding: "8px",
                        borderRadius: 8,
                        border: "1px solid #ddd",
                        background: "#fff",
                        width: 120,
                        fontSize: "12px",
                    }}
                />
                <button
                    onClick={sendPrivateMessage}
                    disabled={!text.trim() && !file}
                    style={{
                        padding: "12px 20px",
                        backgroundColor: "#007bff",
                        color: "#fff",
                        border: "none",
                        borderRadius: 25,
                        cursor: "pointer",
                        fontSize: "14px",
                        fontWeight: "500",
                        opacity: (!text.trim() && !file) ? 0.5 : 1,
                    }}
                >
                    Gửi
                </button>
            </div>
        </div>
    );
};

export default PrivateChat; 