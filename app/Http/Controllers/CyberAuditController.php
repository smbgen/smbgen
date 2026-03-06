<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CyberAuditController extends Controller
{
    /**
     * Display the cyber audit chat interface
     */
    public function index(): View
    {
        return view('cyber-audit.index');
    }

    /**
     * Handle chat messages and return AI responses
     */
    public function chat(Request $request): JsonResponse
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $userMessage = $request->input('message');
        $user = auth()->user();

        // Store the conversation in session for context
        $conversation = session('cyber_audit_conversation', []);
        $conversation[] = [
            'role' => 'user',
            'content' => $userMessage,
            'timestamp' => now(),
        ];

        // Generate AI response based on the message
        $aiResponse = $this->generateCyberAuditResponse($userMessage, $conversation);

        $conversation[] = [
            'role' => 'assistant',
            'content' => $aiResponse,
            'timestamp' => now(),
        ];

        // Keep only last 10 messages for context
        if (count($conversation) > 10) {
            $conversation = array_slice($conversation, -10);
        }

        session(['cyber_audit_conversation' => $conversation]);

        return response()->json([
            'response' => $aiResponse,
            'conversation' => $conversation,
        ]);
    }

    /**
     * Generate AI response for cyber audit questions
     */
    private function generateCyberAuditResponse(string $message, array $conversation): string
    {
        $message = strtolower($message);

        // Check for specific cyber audit topics
        if (str_contains($message, 'password') || str_contains($message, 'authentication')) {
            return $this->getPasswordSecurityResponse();
        }

        if (str_contains($message, 'firewall') || str_contains($message, 'network')) {
            return $this->getNetworkSecurityResponse();
        }

        if (str_contains($message, 'backup') || str_contains($message, 'data')) {
            return $this->getDataBackupResponse();
        }

        if (str_contains($message, 'employee') || str_contains($message, 'training')) {
            return $this->getEmployeeTrainingResponse();
        }

        if (str_contains($message, 'compliance') || str_contains($message, 'gdpr') || str_contains($message, 'hipaa')) {
            return $this->getComplianceResponse();
        }

        if (str_contains($message, 'phishing') || str_contains($message, 'social engineering')) {
            return $this->getPhishingResponse();
        }

        if (str_contains($message, 'hello') || str_contains($message, 'hi') || str_contains($message, 'start')) {
            return $this->getWelcomeResponse();
        }

        // Default response for general questions
        return $this->getGeneralResponse();
    }

    private function getWelcomeResponse(): string
    {
        return "🔒 Welcome to your Cyber Audit Assistant! I'm here to help you assess and improve your cybersecurity posture. 

I can help you with:
• Password and authentication policies
• Network security and firewalls
• Data backup and recovery
• Employee security training
• Compliance requirements (GDPR, HIPAA, etc.)
• Phishing and social engineering awareness

What aspect of cybersecurity would you like to audit first?";
    }

    private function getPasswordSecurityResponse(): string
    {
        return '🔐 **Password Security Assessment**

Here are key areas to audit for password security:

**Current Best Practices:**
✅ Minimum 12 characters
✅ Mix of uppercase, lowercase, numbers, symbols
✅ No common words or patterns
✅ Unique passwords for each account
✅ Regular password changes (90 days)

**Questions to assess your current state:**
1. Do you enforce strong password policies?
2. Do you use multi-factor authentication (MFA)?
3. Do you have a password manager policy?
4. How often do you require password changes?
5. Do you check for compromised passwords?

**Immediate Actions:**
• Implement MFA for all critical accounts
• Use a password manager for your team
• Enable breach monitoring
• Create a password policy document

Would you like me to help you create a password policy template?';
    }

    private function getNetworkSecurityResponse(): string
    {
        return '🛡️ **Network Security Assessment**

**Critical Network Security Areas:**

**Firewall Configuration:**
✅ Next-generation firewall (NGFW)
✅ Regular rule reviews and updates
✅ Segmentation of networks
✅ Intrusion detection/prevention

**WiFi Security:**
✅ WPA3 encryption
✅ Separate guest network
✅ Strong WiFi passwords
✅ Regular network monitoring

**Questions to assess:**
1. Do you have a business-grade firewall?
2. Is your WiFi properly secured?
3. Do you monitor network traffic?
4. Do you have network segmentation?
5. Are all devices updated regularly?

**Immediate Actions:**
• Upgrade to business-grade firewall
• Implement network monitoring
• Create network security policies
• Regular security updates

Would you like a network security checklist?';
    }

    private function getDataBackupResponse(): string
    {
        return '💾 **Data Backup & Recovery Assessment**

**Backup Best Practices:**

**3-2-1 Backup Rule:**
✅ 3 copies of data
✅ 2 different storage types
✅ 1 offsite backup

**Backup Types:**
• Full backups (weekly)
• Incremental backups (daily)
• Differential backups (as needed)

**Questions to assess:**
1. How often do you backup data?
2. Where are backups stored?
3. Have you tested data recovery?
4. Are backups encrypted?
5. How long do you retain backups?

**Critical Actions:**
• Implement automated backups
• Test recovery procedures monthly
• Store backups offsite/cloud
• Encrypt all backup data
• Document recovery procedures

**Recovery Time Objectives:**
• RTO: How quickly can you recover?
• RPO: How much data can you afford to lose?

Would you like help creating a backup strategy?';
    }

    private function getEmployeeTrainingResponse(): string
    {
        return '👥 **Employee Security Training Assessment**

**Essential Training Topics:**

**Core Security Awareness:**
✅ Password security
✅ Phishing recognition
✅ Social engineering awareness
✅ Data handling procedures
✅ Incident reporting

**Training Frequency:**
• Initial training for new employees
• Quarterly refresher training
• Annual comprehensive training
• Ongoing security reminders

**Questions to assess:**
1. Do you have a security training program?
2. How often do employees receive training?
3. Do you test employees with phishing simulations?
4. Do employees know how to report incidents?
5. Is security part of your company culture?

**Immediate Actions:**
• Create security awareness program
• Implement phishing simulations
• Establish incident reporting procedures
• Make security part of onboarding
• Regular security newsletters

**Training Resources:**
• Online security courses
• Phishing simulation tools
• Security awareness videos
• Interactive training modules

Would you like help creating a training program?';
    }

    private function getComplianceResponse(): string
    {
        return '📋 **Compliance Assessment**

**Common Compliance Frameworks:**

**GDPR (EU Data Protection):**
✅ Data minimization
✅ Consent management
✅ Right to be forgotten
✅ Data breach notification
✅ Privacy by design

**HIPAA (Healthcare):**
✅ Administrative safeguards
✅ Physical safeguards
✅ Technical safeguards
✅ Privacy rule compliance
✅ Security rule compliance

**PCI DSS (Payment Cards):**
✅ Network security
✅ Access control
✅ Vulnerability management
✅ Security monitoring
✅ Security policies

**Questions to assess:**
1. What industry regulations apply to you?
2. Do you have compliance documentation?
3. When was your last compliance audit?
4. Do you have a compliance officer?
5. Are you prepared for audits?

**Immediate Actions:**
• Identify applicable regulations
• Create compliance documentation
• Implement required controls
• Regular compliance audits
• Staff compliance training

Would you like help identifying your compliance requirements?';
    }

    private function getPhishingResponse(): string
    {
        return '🎣 **Phishing & Social Engineering Assessment**

**Common Attack Vectors:**

**Email Phishing:**
✅ Suspicious sender addresses
✅ Urgent action requests
✅ Grammar/spelling errors
✅ Suspicious links/attachments
✅ Requests for sensitive data

**Social Engineering:**
✅ Pretexting (fake scenarios)
✅ Baiting (physical media)
✅ Quid pro quo (exchange)
✅ Tailgating (physical access)

**Questions to assess:**
1. Do employees know phishing red flags?
2. Do you have phishing simulation programs?
3. Is there a clear reporting process?
4. Do you block suspicious emails?
5. Are employees trained on social engineering?

**Immediate Actions:**
• Implement phishing simulations
• Create incident reporting procedures
• Use email security tools
• Regular security awareness training
• Test incident response procedures

**Red Flags to Train On:**
• Urgent requests for money/information
• Suspicious sender addresses
• Grammar/spelling errors
• Requests for login credentials
• Unexpected attachments

Would you like help setting up phishing simulations?';
    }

    private function getGeneralResponse(): string
    {
        return '🔍 **General Cybersecurity Assessment**

I can help you with specific areas of cybersecurity. Here are some key topics to consider:

**Quick Security Checklist:**
✅ Strong passwords and MFA
✅ Regular software updates
✅ Firewall and antivirus
✅ Data backups
✅ Employee training
✅ Incident response plan

**Risk Assessment Areas:**
• Data classification
• Access controls
• Network security
• Physical security
• Vendor management
• Business continuity

**Next Steps:**
1. Identify your most critical assets
2. Assess current security measures
3. Prioritize security improvements
4. Create security policies
5. Implement monitoring

What specific area would you like to focus on? I can provide detailed guidance for:
• Password security
• Network protection
• Data backup
• Employee training
• Compliance requirements
• Phishing awareness';
    }

    /**
     * Clear the conversation history
     */
    public function clearHistory(): JsonResponse
    {
        session()->forget('cyber_audit_conversation');

        return response()->json([
            'message' => 'Conversation history cleared',
        ]);
    }
}
