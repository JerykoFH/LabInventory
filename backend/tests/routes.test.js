const request = require('supertest');
const app = require('../server');
const User = require('../src/models/User');

describe('RBAC Global Routes', () => {
  let adminToken;
  let stafLabToken;

  beforeEach(async () => {
    // Create admin user
    await User.create({
      name: 'Admin',
      email: 'admin@example.com',
      password: 'password123',
      role: 'admin'
    });

    // Create staf_lab user
    await User.create({
      name: 'Staf Lab',
      email: 'staf@example.com',
      password: 'password123',
      role: 'staf_lab'
    });

    // Get Admin Token
    const adminRes = await request(app)
      .post('/api/auth/login')
      .send({ email: 'admin@example.com', password: 'password123' });
    adminToken = adminRes.body.token;

    // Get Staf Lab Token
    const stafRes = await request(app)
      .post('/api/auth/login')
      .send({ email: 'staf@example.com', password: 'password123' });
    stafLabToken = stafRes.body.token;
  });

  it('should reject admin from accessing global routes', async () => {
    const res = await request(app)
      .get('/api/global/assets')
      .set('Authorization', `Bearer ${adminToken}`);
    
    // authorize middleware should return 403 Forbidden
    expect(res.statusCode).toEqual(403);
    expect(res.body.success).toBe(false);
  });

  it('should allow staf_lab to access global routes', async () => {
    const res = await request(app)
      .get('/api/global/assets')
      .set('Authorization', `Bearer ${stafLabToken}`);
    
    // allow, so it either returns 200 (if empty/success) or something else, but not 401/403
    expect(res.statusCode).not.toEqual(401);
    expect(res.statusCode).not.toEqual(403);
    expect(res.body.success).toBe(true);
  });
});
