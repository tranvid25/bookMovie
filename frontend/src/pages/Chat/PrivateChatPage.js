import React, { useState } from "react";
import ChatRoomList from "./ChatRoomList";
import PrivateChat from "./PrivateChat";

const PrivateChatPage = () => {
    const [selectedRoom, setSelectedRoom] = useState(null);

    const handleSelectRoom = (room) => {
        setSelectedRoom(room);
    };

    const handleBackToList = () => {
        setSelectedRoom(null);
    };

    return (
        <div style={{ padding: "20px", minHeight: "100vh", backgroundColor: "#f5f5f5" }}>
            {!selectedRoom ? (
                <ChatRoomList onSelectRoom={handleSelectRoom} />
            ) : (
                <div>
                    <div style={{
                        display: "flex",
                        alignItems: "center",
                        marginBottom: "20px",
                        padding: "10px 20px",
                        backgroundColor: "#fff",
                        borderRadius: "8px",
                        boxShadow: "0 2px 4px rgba(0,0,0,0.1)"
                    }}>
                        <button
                            onClick={handleBackToList}
                            style={{
                                padding: "8px 16px",
                                backgroundColor: "#6c757d",
                                color: "#fff",
                                border: "none",
                                borderRadius: "6px",
                                cursor: "pointer",
                                marginRight: "15px",
                                fontSize: "14px",
                            }}
                        >
                            ← Quay lại
                        </button>
                        <h3 style={{ margin: 0, color: "#007bff" }}>
                            {selectedRoom.name || `Phòng ${selectedRoom.id}`}
                        </h3>
                    </div>
                    <PrivateChat
                        roomId={selectedRoom.id}
                        roomName={selectedRoom.name}
                    />
                </div>
            )}
        </div>
    );
};

export default PrivateChatPage; 