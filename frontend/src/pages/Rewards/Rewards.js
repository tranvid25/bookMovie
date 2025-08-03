import React, { useEffect, useState } from 'react';
import { Card, Button, Row, Col, Typography, Progress, Badge, Modal, message, List, Avatar } from 'antd';
import { GiftOutlined, TrophyOutlined, ShoppingCartOutlined, HistoryOutlined } from '@ant-design/icons';
import { baseService } from '../../services/baseService';
import { TOKEN } from '../../util/settings/config';

const { Title, Text } = Typography;

// CSS styles cho rewards page
const styles = {
    rewardsContainer: {
        padding: '24px',
        minHeight: '100vh',
        background: '#f5f5f5'
    },
    rewardCard: {
        height: '100%',
        display: 'flex',
        flexDirection: 'column',
        borderRadius: '8px',
        overflow: 'hidden',
        boxShadow: '0 2px 8px rgba(0,0,0,0.1)',
        transition: 'all 0.3s ease'
    },
    cardBody: {
        flex: 1,
        display: 'flex',
        flexDirection: 'column',
        padding: '16px'
    },
    cardContent: {
        flex: 1,
        display: 'flex',
        flexDirection: 'column'
    },
    cardFooter: {
        marginTop: 'auto',
        paddingTop: '12px'
    },
    rewardImage: {
        height: '200px',
        objectFit: 'cover',
        width: '100%'
    },
    placeholderImage: {
        height: '200px',
        background: '#f0f0f0',
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center',
        width: '100%'
    },
    rewardTitle: {
        margin: 0,
        marginBottom: '8px',
        fontSize: '16px',
        fontWeight: 'bold',
        lineHeight: '1.4'
    },
    rewardDescription: {
        marginBottom: '8px',
        fontSize: '14px',
        lineHeight: '1.5',
        color: '#666'
    },
    pointsInfo: {
        marginBottom: '4px',
        fontSize: '13px',
        color: '#999'
    },
    pointsNeeded: {
        marginBottom: '8px',
        fontSize: '13px',
        color: '#fa8c16'
    },
    exchangeButton: {
        width: '100%',
        height: '40px',
        borderRadius: '6px',
        fontWeight: '500'
    }
};

const Rewards = () => {
    const [rewards, setRewards] = useState([]);
    const [userProfile, setUserProfile] = useState(null);
    const [loading, setLoading] = useState(false);
    const [exchangeModalVisible, setExchangeModalVisible] = useState(false);
    const [selectedReward, setSelectedReward] = useState(null);

    // Tạo instance của baseService
    const baseServiceInstance = new baseService();

    useEffect(() => {
        // Kiểm tra user đã đăng nhập chưa
        const userLogin = JSON.parse(localStorage.getItem('USER_LOGIN') || '{}');
        const token = localStorage.getItem('accessToken');

        console.log('User login status:', !!userLogin.id);
        console.log('Token exists:', !!token);

        if (userLogin.id && token) {
            fetchUserProfile();
            fetchRewards();
        } else {
            console.log('User not logged in or no token');
            message.warning('Vui lòng đăng nhập để xem thông tin điểm tích lũy');
        }
    }, []);

    const fetchUserProfile = async () => {
        try {
            // Kiểm tra token
            const token = localStorage.getItem('accessToken');
            console.log('Token exists:', !!token);
            console.log('Token value:', token ? token.substring(0, 20) + '...' : 'null');

            console.log('Fetching user profile for rewards...');
            const response = await baseServiceInstance.get('/api/auth/me');
            console.log('User profile response (Rewards):', response.data);

            if (response.data.status === 200) {
                console.log('Setting user profile with points:', response.data.user.diem_tich_luy);
                setUserProfile(response.data.user);
            }
        } catch (error) {
            console.error('Error fetching user profile:', error);
            console.error('Error response:', error.response?.data);

            // Kiểm tra nếu lỗi 401 (Unauthorized)
            if (error.response?.status === 401) {
                console.error('User not authenticated. Please login first.');
                message.error('Vui lòng đăng nhập để xem thông tin điểm tích lũy');
            }
        }
    };

    const fetchRewards = async () => {
        try {
            const response = await baseServiceInstance.get('/api/auth/rewards');
            if (response.data.status === 200) {
                setRewards(response.data.content);
            }
        } catch (error) {
            console.error('Error fetching rewards:', error);
        }
    };

    const handleExchangeReward = async () => {
        if (!selectedReward) return;

        setLoading(true);
        try {
            const response = await baseServiceInstance.post('/api/auth/rewards/exchange', {
                reward_id: selectedReward.id
            });

            if (response.data.status === 200) {
                message.success('Đổi quà thành công! Quà sẽ được giao sau 3 ngày.');
                setExchangeModalVisible(false);
                setSelectedReward(null);
                fetchUserProfile(); // Refresh user profile để cập nhật điểm
                fetchRewards();
            }
        } catch (error) {
            message.error(error.response?.data?.message || 'Có lỗi xảy ra khi đổi quà');
        } finally {
            setLoading(false);
        }
    };

    const getRankColor = (rank) => {
        const colors = {
            'thuong': 'default',
            'bac': 'blue',
            'vang': 'gold',
            'kimcuong': 'purple'
        };
        return colors[rank] || 'default';
    };

    const getRankName = (rank) => {
        const names = {
            'thuong': 'Thành viên thường',
            'bac': 'Thành viên bạc',
            'vang': 'Thành viên vàng',
            'kimcuong': 'Thành viên kim cương'
        };
        return names[rank] || 'Thành viên thường';
    };

    const getNextRankProgress = (currentPoints, currentRank) => {
        const rankThresholds = {
            'thuong': 500,
            'bac': 1500,
            'vang': 3000,
            'kimcuong': null
        };

        const nextThreshold = rankThresholds[currentRank];
        if (!nextThreshold) return 100;

        const progress = (currentPoints / nextThreshold) * 100;
        return Math.min(Math.round(progress * 100) / 100, 100);
    };

    console.log('User profile in Rewards:', userProfile);

    return (
        <div style={styles.rewardsContainer}>
            <Title level={2} className="mb-8 text-center">
                <GiftOutlined /> Quà Tặng & Đổi Điểm
            </Title>

            {/* User Points Section */}
            {userProfile && (
                <Card className="mb-8" style={{ background: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)', color: 'white' }}>
                    <Row gutter={16} align="middle">
                        <Col span={8}>
                            <div className="text-center">
                                <TrophyOutlined style={{ fontSize: '48px', marginBottom: '16px' }} />
                                <Title level={3} style={{ color: 'white', margin: 0 }}>
                                    {userProfile.diem_tich_luy || 0} điểm
                                </Title>
                                <Text style={{ color: 'white' }}>Điểm tích lũy hiện tại</Text>
                            </div>
                        </Col>
                        <Col span={8}>
                            <div className="text-center">
                                <Badge.Ribbon text={getRankName(userProfile.rank || 'thuong')} color={getRankColor(userProfile.rank || 'thuong')}>
                                    <div style={{ padding: '20px', background: 'rgba(255,255,255,0.1)', borderRadius: '8px' }}>
                                        <Title level={4} style={{ color: 'white', margin: 0 }}>
                                            {getRankName(userProfile.rank || 'thuong')}
                                        </Title>
                                    </div>
                                </Badge.Ribbon>
                            </div>
                        </Col>
                        <Col span={8}>
                            <div>
                                <Text style={{ color: 'white' }}>Tiến độ rank tiếp theo:</Text>
                                <Progress
                                    percent={getNextRankProgress(userProfile.diem_tich_luy || 0, userProfile.rank || 'thuong')}
                                    strokeColor="#fff"
                                    showInfo={false}
                                />
                            </div>
                        </Col>
                    </Row>

                    {/* Rank Info */}
                    {userProfile.rank_info && (
                        <div className="mt-4" style={{ color: 'white' }}>
                            <Title level={5} style={{ color: 'white', marginBottom: '8px' }}>
                                Quyền lợi hiện tại:
                            </Title>
                            <ul style={{ margin: 0, paddingLeft: '20px' }}>
                                {userProfile.rank_info.benefits.map((benefit, index) => (
                                    <li key={index} style={{ color: 'white' }}>✓ {benefit}</li>
                                ))}
                            </ul>
                        </div>
                    )}
                </Card>
            )}

            {/* Recent Points History */}
            {userProfile && userProfile.recent_points && userProfile.recent_points.length > 0 && (
                <Card title="Lịch sử điểm gần đây" className="mb-6">
                    <List
                        itemLayout="horizontal"
                        dataSource={userProfile.recent_points}
                        renderItem={(point) => (
                            <List.Item>
                                <List.Item.Meta
                                    avatar={<HistoryOutlined style={{ fontSize: '20px', color: point.loai === '+' ? '#52c41a' : '#ff4d4f' }} />}
                                    title={
                                        <div>
                                            <Text strong>{point.mo_ta}</Text>
                                            <Badge
                                                count={point.loai === '+' ? `+${point.so_diem}` : `-${point.so_diem}`}
                                                style={{
                                                    backgroundColor: point.loai === '+' ? '#52c41a' : '#ff4d4f',
                                                    marginLeft: '8px'
                                                }}
                                            />
                                        </div>
                                    }
                                    description={
                                        <Text type="secondary">
                                            {new Date(point.created_at).toLocaleDateString('vi-VN')} - {new Date(point.created_at).toLocaleTimeString('vi-VN')}
                                        </Text>
                                    }
                                />
                            </List.Item>
                        )}
                    />
                </Card>
            )}

            {/* Rewards Grid */}
            <Title level={3} className="mb-6">
                <ShoppingCartOutlined /> Danh Sách Quà Tặng
            </Title>

            <Row gutter={[16, 16]}>
                {rewards.map((reward) => {
                    const currentPoints = userProfile?.diem_tich_luy || 0;
                    const canExchange = currentPoints >= reward.so_diem_can;
                    const pointsNeeded = reward.so_diem_can - currentPoints;

                    return (
                        <Col xs={24} sm={12} lg={8} xl={6} key={reward.id}>
                            <Card
                                hoverable
                                style={styles.rewardCard}
                                cover={
                                    reward.hinh_anh ? (
                                        <img
                                            alt={reward.ten_qua}
                                            src={reward.hinh_anh}
                                            style={styles.rewardImage}
                                        />
                                    ) : (
                                        <div style={styles.placeholderImage}>
                                            <GiftOutlined style={{ fontSize: '48px', color: '#ccc' }} />
                                        </div>
                                    )
                                }
                                bodyStyle={styles.cardBody}
                            >
                                <div style={styles.cardContent}>
                                    <Card.Meta
                                        title={
                                            <div style={{ marginBottom: '8px' }}>
                                                <Title level={4} style={styles.rewardTitle}>
                                                    {reward.ten_qua}
                                                </Title>
                                                <Badge
                                                    count={reward.so_luong_con}
                                                    style={{
                                                        backgroundColor: reward.so_luong_con > 0 ? '#52c41a' : '#ff4d4f',
                                                        marginLeft: '4px'
                                                    }}
                                                />
                                            </div>
                                        }
                                        description={
                                            <div>
                                                <Text style={styles.rewardDescription}>
                                                    {reward.mo_ta}
                                                </Text>
                                                <Text type="secondary" style={styles.pointsInfo}>
                                                    Cần: {reward.so_diem_can} điểm
                                                </Text>
                                                {!canExchange && (
                                                    <Text type="warning" style={styles.pointsNeeded}>
                                                        Cần thêm {pointsNeeded} điểm
                                                    </Text>
                                                )}
                                            </div>
                                        }
                                    />
                                </div>

                                <div style={styles.cardFooter}>
                                    <Button
                                        type={canExchange ? "primary" : "default"}
                                        icon={<GiftOutlined />}
                                        onClick={() => {
                                            if (canExchange) {
                                                setSelectedReward(reward);
                                                setExchangeModalVisible(true);
                                            }
                                        }}
                                        disabled={!canExchange}
                                        block
                                        style={styles.exchangeButton}
                                    >
                                        {canExchange ? `Đổi quà (${reward.so_diem_can} điểm)` : 'Không đủ điểm'}
                                    </Button>
                                </div>
                            </Card>
                        </Col>
                    );
                })}
            </Row>

            {/* Exchange Modal */}
            <Modal
                title="Xác nhận đổi quà"
                open={exchangeModalVisible}
                onOk={handleExchangeReward}
                onCancel={() => {
                    setExchangeModalVisible(false);
                    setSelectedReward(null);
                }}
                confirmLoading={loading}
                okText="Xác nhận đổi quà"
                cancelText="Hủy"
            >
                {selectedReward && (
                    <div>
                        <p>Bạn có chắc chắn muốn đổi quà <strong>{selectedReward.ten_qua}</strong>?</p>
                        <p>Số điểm cần: <strong>{selectedReward.so_diem_can}</strong></p>
                        <p>Số điểm hiện tại: <strong>{userProfile?.diem_tich_luy || 0}</strong></p>
                        <p>Số điểm còn lại sau khi đổi: <strong>{(userProfile?.diem_tich_luy || 0) - selectedReward.so_diem_can}</strong></p>
                    </div>
                )}
            </Modal>
        </div>
    );
};

export default Rewards;
