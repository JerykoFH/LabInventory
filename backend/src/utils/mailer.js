const nodemailer = require('nodemailer');

const createTransporter = () => {
    return nodemailer.createTransport({
        host: process.env.SMTP_HOST,
        port: process.env.SMTP_PORT || 465,
        secure: process.env.SMTP_PORT == 465, 
        auth: {
            user: process.env.SMTP_USER,
            pass: process.env.SMTP_PASS,
        },
    });
};

/**
 * Fungsi untuk mengirim email notifikasi
 * @param {string} to - Alamat email tujuan
 * @param {string} subject - Subjek email
 * @param {string} htmlContent - Isi email dalam format HTML
 */
const sendEmail = async (to, subject, htmlContent) => {
    if (!process.env.SMTP_HOST || !process.env.SMTP_USER || !process.env.SMTP_PASS) {
        console.warn('[Mailer] Konfigurasi SMTP di .env belum lengkap. Email tidak dapat dikirim secara nyata.');
        return;
    }

    try {
        const transporter = createTransporter();
        const info = await transporter.sendMail({
            from: `"Sistem LabInventory" <${process.env.SMTP_USER}>`,
            to,
            subject,
            html: htmlContent,
        });

        console.log(`[Mailer] Email berhasil dikirim ke ${to} (Message ID: ${info.messageId})`);
    } catch (error) {
        console.error('[Mailer] Gagal mengirim email:', error.message);
    }
};

module.exports = { sendEmail };
