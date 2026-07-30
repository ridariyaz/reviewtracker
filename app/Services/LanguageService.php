<?php

namespace App\Services;

class LanguageService
{
    private array $translations = [
        'en' => [
            'dir' => 'ltr',
            'headline' => 'How was your experience?',
            'subheadline' => 'Your feedback takes only 5 seconds and helps us deliver great service!',
            'great' => 'Great!',
            'ok' => 'It was OK',
            'bad' => 'Needs Improvement',
            'improve_title' => 'Tell us how we can improve',
            'improve_sub' => 'Your notes go directly to management to help us improve our service.',
            'placeholder' => 'What worked well, what didn\'t, or anything specific we should fix...',
            'submit' => 'Submit Feedback',
            'thankyou_title' => 'Thank You!',
            'thankyou_sub' => 'Your feedback has been received and sent directly to our management team.',
            'multi_title' => 'Where would you like to review us?',
            'multi_sub' => 'Choose one or multiple platforms below to post your review:',
            'post_on' => 'Post on',
        ],
        'ml' => [
            'dir' => 'ltr',
            'headline' => 'നിങ്ങളുടെ അനുഭവം എങ്ങനെയായിരുന്നു?',
            'subheadline' => 'നിങ്ങളുടെ അഭിപ്രായം 5 സെക്കൻഡ് മാത്രം എടുക്കും, മികച്ച സേവനം നൽകാൻ ഞങ്ങളെ സഹായിക്കും!',
            'great' => 'വളരെ നന്നായിരുന്നു!',
            'ok' => 'ഓക്കെ ആയിരുന്നു',
            'bad' => 'മെച്ചപ്പെടേണ്ടതുണ്ട്',
            'improve_title' => 'ഞങ്ങൾക്ക് എങ്ങനെ മെച്ചപ്പെടാമെന്ന് പറയുക',
            'improve_sub' => 'നിങ്ങളുടെ നിർദ്ദേശങ്ങൾ മാനേജ്‌മെന്റിന് നേരിട്ട് ലഭ്യമാകും.',
            'placeholder' => 'എന്താണ് നന്നായത്, എന്താണ് മെച്ചപ്പെടേണ്ടത്...',
            'submit' => 'അഭിപ്രായം അയക്കുക',
            'thankyou_title' => 'നന്ദി!',
            'thankyou_sub' => 'നിങ്ങളുടെ അഭിപ്രായം മാനേജ്‌മെന്റ് ടീമിന് ലഭിച്ചു.',
            'multi_title' => 'എവിടെയാണ് വിലയിരുത്താൻ ആഗ്രഹിക്കുന്നത്?',
            'multi_sub' => 'താഴെയുള്ള പ്ലാറ്റ്‌ഫോമുകൾ തിരഞ്ഞെടുക്കുക:',
            'post_on' => 'പോസ്റ്റ് ചെയ്യുക',
        ],
        'hi' => [
            'dir' => 'ltr',
            'headline' => 'आपका अनुभव कैसा रहा?',
            'subheadline' => 'आपकी प्रतिक्रिया में केवल 5 सेकंड लगते हैं और हमें बेहतर सेवा देने में मदद करती है!',
            'great' => 'बहुत अच्छा!',
            'ok' => 'ठीक-ठाक था',
            'bad' => 'सुधार की जरूरत है',
            'improve_title' => 'बताएं कि हम कैसे सुधार कर सकते हैं',
            'improve_sub' => 'आपकी टिप्पणियां सीधे प्रबंधन तक पहुंचती हैं।',
            'placeholder' => 'क्या अच्छा रहा, क्या बेहतर हो सकता है...',
            'submit' => 'प्रतिक्रिया भेजें',
            'thankyou_title' => 'धन्यवाद!',
            'thankyou_sub' => 'आपकी प्रतिक्रिया हमारे प्रबंधन टीम को प्राप्त हो गई है।',
            'multi_title' => 'आप हमें कहां समीक्षा देना चाहते हैं?',
            'multi_sub' => 'नीचे दिए गए प्लेटफॉर्म चुनें:',
            'post_on' => 'समीक्षा दें',
        ],
        'ar' => [
            'dir' => 'rtl',
            'headline' => 'كيف كانت تجربتك؟',
            'subheadline' => 'تستغرق ملاحظاتك 5 ثوانٍ فقط وتساعدنا في تقديم خدمة ممتازة!',
            'great' => 'ممتازة!',
            'ok' => 'كانت حسنة',
            'bad' => 'تحتاج تحسين',
            'improve_title' => 'أخبرنا كيف يمكننا التحسين',
            'improve_sub' => 'تنتقل ملاحظاتك مباشرة إلى الإدارة لتحسين خدمتنا.',
            'placeholder' => 'ما الذي كان جيدًا، وما الذي يحتاج تحسينًا...',
            'submit' => 'إرسال الملاحظات',
            'thankyou_title' => 'شكرا لك!',
            'thankyou_sub' => 'تم استلام ملاحظاتك وإرسالها مباشرة إلى فريق الإدارة لدينا.',
            'multi_title' => 'أين ترغب في كتابة مراجعتك؟',
            'multi_sub' => 'اختر منصة أو أكثر أدناه لنشر مراجعتك:',
            'post_on' => 'نشر على',
        ],
    ];

    public function getTranslations(?string $lang): array
    {
        $code = strtolower((string) $lang);

        return $this->translations[$code] ?? $this->translations['en'];
    }
}
