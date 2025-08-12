import React, { useState, useEffect } from "react";
import axios from "axios";
import { TOKEN } from "../../util/settings/config";

const ChatRoomList = ({ onSelectRoom }) => {
    const [rooms, setRooms] = useState([]);
    const [loading, setLoading] = useState(true);
    const [showCreateForm, setShowCreateForm] = useState(false);
    const [newRoom, setNewRoom] = useState({ name: "", description: "" });

    const accessToken = localStorage.getItem(TOKEN);

    // Lấy danh sách phòng chat
    useEffect(() => {
        if (!accessToken) return;

        axios
            .get("http://localhost:8000/api/auth/chatrooms", {
                headers: { Authorization: `Bearer ${accessToken}` },
            })
            .then((res) => {
                console.log('API Response:', res.data);
                if (res.data && res.data.rooms) {
                    setRooms(res.data.rooms);
                } else {
                    console.warn('No rooms data in response:', res.data);
                }
            })
            .catch((err) => {
                console.error("Lỗi lấy danh sách phòng:", err);
                if (err.response) {
                    console.error("Response status:", err.response.status);
                    console.error("Response data:", err.response.data);
                }
            })
            .finally(() => {
                setLoading(false);
            });
    }, [accessToken]);

    const createRoom = async () => {
        if (!newRoom.name.trim()) return;

        try {
            const response = await axios.post(
                "http://localhost:8000/api/auth/chatrooms",
                newRoom,
                {
                    headers: { Authorization: `Bearer ${accessToken}` },
                }
            );

            if (response.data && response.data.room) {
                setRooms((prev) => [...prev, response.data.room]);
                setNewRoom({ name: "", description: "" });
                setShowCreateForm(false);
            }
        } catch (err) {
            console.error("Lỗi tạo phòng:", err);
        }
    };

    if (loading) {
        return (
            <div style={{ textAlign: "center", padding: "20px" }}>
                <p>Đang tải danh sách phòng...</p>
            </div>
        );
    }

    return (
        <div
            style={{
                maxWidth: 400,
                margin: "20px auto",
                padding: "20px",
                backgroundColor: "#fff",
                borderRadius: "12px",
                boxShadow: "0 4px 6px rgba(0, 0, 0, 0.1)",
            }}
        >
            <div style={{
                display: "flex",
                justifyContent: "space-between",
                alignItems: "center",
                marginBottom: "20px",
                paddingBottom: "10px",
                borderBottom: "2px solid #007bff"
            }}>
                <h3 style={{ margin: 0, color: "#007bff" }}>🏠 Phòng Chat</h3>
                <button
                    onClick={() => setShowCreateForm(!showCreateForm)}
                    style={{
                        padding: "8px 16px",
                        backgroundColor: "#28a745",
                        color: "#fff",
                        border: "none",
                        borderRadius: "20px",
                        cursor: "pointer",
                        fontSize: "14px",
                    }}
                >
                    {showCreateForm ? "Hủy" : "+ Tạo phòng"}
                </button>
            </div>

            {showCreateForm && (
                <div
                    style={{
                        padding: "16px",
                        backgroundColor: "#f8f9fa",
                        borderRadius: "8px",
                        marginBottom: "16px",
                        border: "1px solid #e9ecef",
                    }}
                >
                    <h4 style={{ margin: "0 0 12px 0", color: "#333" }}>
                        Tạo phòng mới
                    </h4>
                    <input
                        type="text"
                        placeholder="Tên phòng"
                        value={newRoom.name}
                        onChange={(e) =>
                            setNewRoom((prev) => ({ ...prev, name: e.target.value }))
                        }
                        style={{
                            width: "100%",
                            padding: "10px",
                            marginBottom: "8px",
                            borderRadius: "6px",
                            border: "1px solid #ddd",
                        }}
                    />
                    <textarea
                        placeholder="Mô tả phòng (tùy chọn)"
                        value={newRoom.description}
                        onChange={(e) =>
                            setNewRoom((prev) => ({ ...prev, description: e.target.value }))
                        }
                        style={{
                            width: "100%",
                            padding: "10px",
                            marginBottom: "12px",
                            borderRadius: "6px",
                            border: "1px solid #ddd",
                            resize: "vertical",
                            minHeight: "60px",
                        }}
                    />
                    <div style={{ display: "flex", gap: "8px" }}>
                        <button
                            onClick={createRoom}
                            disabled={!newRoom.name.trim()}
                            style={{
                                padding: "8px 16px",
                                backgroundColor: "#007bff",
                                color: "#fff",
                                border: "none",
                                borderRadius: "6px",
                                cursor: "pointer",
                                opacity: newRoom.name.trim() ? 1 : 0.5,
                            }}
                        >
                            Tạo
                        </button>
                        <button
                            onClick={() => {
                                setShowCreateForm(false);
                                setNewRoom({ name: "", description: "" });
                            }}
                            style={{
                                padding: "8px 16px",
                                backgroundColor: "#6c757d",
                                color: "#fff",
                                border: "none",
                                borderRadius: "6px",
                                cursor: "pointer",
                            }}
                        >
                            Hủy
                        </button>
                    </div>
                </div>
            )}

            <div style={{ maxHeight: "400px", overflowY: "auto" }}>
                {rooms.length === 0 ? (
                    <div style={{ textAlign: "center", color: "#666", padding: "20px" }}>
                        <p>Chưa có phòng nào</p>
                        <small>Tạo phòng đầu tiên để bắt đầu chat!</small>
                    </div>
                ) : (
                    rooms.map((room) => (
                        <div
                            key={room.id}
                            onClick={() => onSelectRoom(room)}
                            style={{
                                padding: "12px",
                                marginBottom: "8px",
                                backgroundColor: "#f8f9fa",
                                borderRadius: "8px",
                                border: "1px solid #e9ecef",
                                cursor: "pointer",
                                transition: "all 0.2s ease",
                            }}
                            onMouseEnter={(e) => {
                                e.target.style.backgroundColor = "#e9ecef";
                            }}
                            onMouseLeave={(e) => {
                                e.target.style.backgroundColor = "#f8f9fa";
                            }}
                        >
                            <div style={{ fontWeight: "500", color: "#333", marginBottom: "4px" }}>
                                {room.name || `Phòng ${room.id}`}
                            </div>
                            {room.description && (
                                <div style={{ fontSize: "12px", color: "#666" }}>
                                    {room.description}
                                </div>
                            )}
                            <div style={{ fontSize: "10px", color: "#999", marginTop: "4px" }}>
                                Tạo lúc: {new Date(room.created_at).toLocaleDateString()}
                            </div>
                        </div>
                    ))
                )}
            </div>
        </div>
    );
};

export default ChatRoomList; 