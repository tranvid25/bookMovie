import React, { useEffect, useState } from 'react';
import { Avatar, Button, Typography, Card, Tag, Progress } from 'antd';
import { useDispatch, useSelector } from 'react-redux';
import { layThongTinNguoiDungAction } from '../../redux/actions/QuanLyNguoiDungAction';
import { USER_LOGIN } from '../../util/settings/config';
import { TrophyOutlined } from '@ant-design/icons';

const Profile = () => {
  const dispatch = useDispatch();
  const { profile } = useSelector(state => state.UserReducer);

  let userLogin = {}
  if (localStorage.getItem(USER_LOGIN)) {
    userLogin = JSON.parse(localStorage.getItem(USER_LOGIN))
  }

  useEffect(() => {
    dispatch(layThongTinNguoiDungAction(userLogin.id))
  }, [])

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
    if (!nextThreshold) return 100; // Đã đạt rank cao nhất

    const progress = (currentPoints / nextThreshold) * 100;
    return Math.min(Math.round(progress * 100) / 100, 100); // Làm tròn về 2 số thập phân
  };

  console.log('profile', profile)
  console.log('Current points from profile:', profile?.diem_tich_luy || 0)

  return (
    <div className="container mx-auto p-6">
      <h3 className='mb-5'>Thông tin người dùng: {profile.name}</h3>

      <div className='row mx-10'>
        <div className='col-4'>
          {profile?.avatar == null || profile?.avatar == ""
            ? <Avatar size={200} style={{ fontSize: '200px', lineHeight: '170px' }} icon={profile.name?.substr(0, 1)} />
            : <div style={{ minWidth: '40px', minHeight: 40, width: 200, height: 200, backgroundSize: 'cover', borderRadius: '50%', backgroundImage: `url(${profile?.avatar})` }} />
          }
        </div>
        <div className='col-8'>
          <div className='col-6'>
            <Typography>
              <pre>Tên Đăng Nhập: {profile.name}</pre>
            </Typography>
          </div>
          <div className='col-6'>
            <Typography>
              <pre>Email: {profile.email}</pre>
            </Typography>
          </div>
          <div className='col-6'>
            <Typography>
              <pre>Loại Tài Khoản: {profile.role}</pre>
            </Typography>
          </div>

          <div className='col-6'>
            <Button href={`/users/edit/${profile.id}`} className='btn-primary bg-primary mt-3 px-5' type='primary' onClick={() => {
              localStorage.setItem('userParams', JSON.stringify(profile));
            }}>Thay đổi thông tin</Button>
          </div>
        </div>
      </div>

      {/* Hiển thị tích lũy điểm thưởng */}
      {profile && (
        <div className='mt-6'>
          <Card title="Tích lũy điểm thưởng" className="mb-4">
            <div className="flex items-center mb-3">
              <TrophyOutlined style={{ fontSize: '24px', marginRight: '8px' }} />
              <Tag color={getRankColor(profile.rank || 'thuong')} size="large">
                {getRankName(profile.rank || 'thuong')}
              </Tag>
            </div>

            <div className="mb-3">
              <div className="flex justify-between mb-2">
                <span>Điểm tích lũy: <strong>{profile.diem_tich_luy || 0}</strong></span>
                <span>Điểm cần để rank tiếp theo</span>
              </div>
              <Progress
                percent={getNextRankProgress(profile.diem_tich_luy || 0, profile.rank || 'thuong')}
                status="active"
                strokeColor={{
                  '0%': '#108ee9',
                  '100%': '#87d068',
                }}
              />
            </div>

            {/* Quyền lợi theo rank */}
            <div className="mt-3">
              <h4>Quyền lợi hiện tại:</h4>
              <ul>
                {(profile.rank || 'thuong') === 'thuong' && (
                  <li className="text-green-600">✓ Giảm giá 5% cho vé phim</li>
                )}
                {(profile.rank || 'thuong') === 'bac' && (
                  <>
                    <li className="text-green-600">✓ Giảm giá 10% cho vé phim</li>
                    <li className="text-green-600">✓ Ưu tiên đặt vé</li>
                  </>
                )}
                {(profile.rank || 'thuong') === 'vang' && (
                  <>
                    <li className="text-green-600">✓ Giảm giá 15% cho vé phim</li>
                    <li className="text-green-600">✓ Ưu tiên đặt vé</li>
                    <li className="text-green-600">✓ Quà tặng sinh nhật</li>
                  </>
                )}
                {(profile.rank || 'thuong') === 'kimcuong' && (
                  <>
                    <li className="text-green-600">✓ Giảm giá 20% cho vé phim</li>
                    <li className="text-green-600">✓ Ưu tiên đặt vé</li>
                    <li className="text-green-600">✓ Quà tặng sinh nhật</li>
                    <li className="text-green-600">✓ Vé xem phim miễn phí</li>
                  </>
                )}
              </ul>
            </div>
          </Card>
        </div>
      )}
    </div>
  );
};

export default Profile;