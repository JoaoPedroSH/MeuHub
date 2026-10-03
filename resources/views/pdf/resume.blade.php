<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $resume->title }}</title>
    <style>
        @page {
            size: A4;
            margin: 16mm 15mm 15mm 15mm;
        }
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10pt;
            line-height: 1.42;
            color: #1f2937;
            background-color: #ffffff;
        }
        .header {
            border-bottom: 2px solid #0f172a;
            padding-bottom: 14px;
            margin-bottom: 18px;
        }
    .header h1 {
            font-size: 20pt;
            font-weight: 700;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
    }
    .profile-photo {
        width: 78px;
        height: 78px;
        object-fit: cover;
        border-radius: 50%;
        float: right;
        margin-left: 16px;
    }
        .contact-info {
            font-size: 9pt;
            color: #4b5563;
            line-height: 1.6;
        }
        .contact-item {
            display: inline-block;
            margin-right: 12px;
        }
        .section {
            margin-bottom: 13px;
            page-break-inside: auto;
        }
        .section-title {
            font-size: 11.5pt;
            font-weight: 700;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            border-bottom: 1px solid #cbd5e1;
            padding-bottom: 4px;
            margin-bottom: 10px;
        }
        .item {
            margin-bottom: 9px;
            page-break-inside: avoid;
        }
        .item-header {
            width: 100%;
            margin-bottom: 2px;
        }
        .item-title {
            font-weight: 700;
            font-size: 10.5pt;
            color: #111827;
        }
        .item-subtitle {
            font-size: 9.5pt;
            color: #374151;
            font-style: italic;
        }
        .item-date {
            float: right;
            font-size: 9pt;
            color: #6b7280;
            font-style: normal;
        }
        .item-desc {
            font-size: 9.5pt;
            color: #374151;
            margin-top: 4px;
            white-space: pre-line;
            line-height: 1.45;
        }
        .skills-grid {
            margin-top: 4px;
        }
        .skill-badge {
            display: inline-block;
            background-color: #f1f5f9;
            color: #1e293b;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 9pt;
            margin-right: 6px;
            margin-bottom: 6px;
            border: 1px solid #e2e8f0;
        }
        .languages-list {
            margin-top: 4px;
        }
        .language-item {
            font-size: 9.5pt;
            margin-bottom: 4px;
        }
        .language-name {
            font-weight: 700;
            color: #1f2937;
        }
        .language-level {
            color: #6b7280;
        }
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
    </style>
</head>
<body>
    @php
        $info = $resume->personal_info ?? [];
        $fullName = !empty($info['full_name']) ? $info['full_name'] : ($resume->title ?: 'Currículo');
    @endphp

    <div class="header">
        @if(!empty($resume->photo_path) && file_exists(public_path('storage/' . $resume->photo_path)))
            <img class="profile-photo" src="{{ public_path('storage/' . $resume->photo_path) }}" alt="Foto profissional">
        @endif
        <h1>{{ $fullName }}</h1>
        <div class="contact-info">
            @if(!empty($info['email']))
                <span class="contact-item"><strong>E-mail:</strong> {{ $info['email'] }}</span>
            @endif
            @if(!empty($info['phone']))
                <span class="contact-item"><strong>Telefone:</strong> {{ $info['phone'] }}</span>
            @endif
            @if(!empty($info['city']))
                <span class="contact-item"><strong>Localização:</strong> {{ $info['city'] }}</span>
            @endif
            @if(!empty($info['linkedin']))
                <span class="contact-item"><strong>LinkedIn:</strong> {{ $info['linkedin'] }}</span>
            @endif
            @if(!empty($info['github']))
                <span class="contact-item"><strong>GitHub:</strong> {{ $info['github'] }}</span>
            @endif
            @if(!empty($info['website']))
                <span class="contact-item"><strong>Portfólio:</strong> {{ $info['website'] }}</span>
            @endif
        </div>
    </div>

    @if(!empty($resume->summary))
        <div class="section">
            <div class="section-title">Resumo Profissional</div>
            <div class="item-desc">{{ $resume->summary }}</div>
        </div>
    @endif

    @if($resume->experiences && $resume->experiences->count() > 0)
        <div class="section">
            <div class="section-title">Experiência Profissional</div>
            @foreach($resume->experiences as $exp)
                <div class="item">
                    <div class="item-header clearfix">
                        <span class="item-date">
                            {{ $exp->start_date }} - {{ $exp->is_current ? 'Atual' : ($exp->end_date ?: 'Presente') }}
                        </span>
                        <div class="item-title">{{ $exp->position }}</div>
                    </div>
                    <div class="item-subtitle">
                        {{ $exp->company }}{{ !empty($exp->location) ? ' — ' . $exp->location : '' }}
                    </div>
                    @if(!empty($exp->description))
                        <div class="item-desc">{{ $exp->description }}</div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    @if($resume->education && $resume->education->count() > 0)
        <div class="section">
            <div class="section-title">Formação Acadêmica</div>
            @foreach($resume->education as $edu)
                <div class="item">
                    <div class="item-header clearfix">
                        @if(!empty($edu->start_date) || !empty($edu->end_date))
                            <span class="item-date">
                                {{ $edu->start_date }} - {{ $edu->end_date ?: 'Atual' }}
                            </span>
                        @endif
                        <div class="item-title">{{ $edu->course }}</div>
                    </div>
                    <div class="item-subtitle">
                        {{ $edu->institution }}{{ !empty($edu->degree) ? ' (' . $edu->degree . ')' : '' }}
                    </div>
                    @if(!empty($edu->description))
                        <div class="item-desc">{{ $edu->description }}</div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    @if($resume->skills && $resume->skills->count() > 0)
        <div class="section">
            <div class="section-title">Habilidades</div>
            <div class="skills-grid">
                @foreach($resume->skills as $skill)
                    <span class="skill-badge">{{ $skill->name }}{{ !empty($skill->level) ? ' (' . $skill->level . ')' : '' }}</span>
                @endforeach
            </div>
        </div>
    @endif

    @if($resume->languages && $resume->languages->count() > 0)
        <div class="section">
            <div class="section-title">Idiomas</div>
            <div class="languages-list">
                @foreach($resume->languages as $lang)
                    <div class="language-item">
                        <span class="language-name">{{ $lang->language }}</span> — <span class="language-level">{{ $lang->level }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    @if($resume->courses && $resume->courses->count() > 0)
        <div class="section">
            <div class="section-title">Cursos</div>
            @foreach($resume->courses as $course)
                <div class="item">
                    <div class="item-header clearfix">
                        @if(!empty($course->date))
                            <span class="item-date">{{ $course->date }}</span>
                        @endif
                        <div class="item-title">{{ $course->name }}</div>
                    </div>
                    @if(!empty($course->institution))
                        <div class="item-subtitle">{{ $course->institution }}</div>
                    @endif
                    @if(!empty($course->description))
                        <div class="item-desc">{{ $course->description }}</div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    @if($resume->certifications && $resume->certifications->count() > 0)
        <div class="section">
            <div class="section-title">Certificações</div>
            @foreach($resume->certifications as $cert)
                <div class="item">
                    <div class="item-header clearfix">
                        @if(!empty($cert->date))
                            <span class="item-date">{{ $cert->date }}{{ !empty($cert->expiration_date) ? ' - ' . $cert->expiration_date : '' }}</span>
                        @endif
                        <div class="item-title">{{ $cert->name }}</div>
                    </div>
                    @if(!empty($cert->institution))
                        <div class="item-subtitle">{{ $cert->institution }}{{ !empty($cert->code) ? ' | Código: ' . $cert->code : '' }}</div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    @if(!empty($resume->additional_info))
        <div class="section">
            <div class="section-title">Informações Adicionais</div>
            <div class="item-desc">{{ $resume->additional_info }}</div>
        </div>
    @endif
</body>
</html>
