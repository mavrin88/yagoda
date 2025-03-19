<template>
  <div class="landing-wrapper">
    <header class="header">
      <div class="container">
        <nav class="header__nav">
          <div class="header__nav-left">
            <div class="header__burger">
              <label class="header__burger-container" @click="menuOpen" v-if="!isShowMenu">
                <input
                  type="checkbox"
                  class="header__burger-input"
                  name="menu-button"
                />
                <span class="header__burger-line"></span>
                <span class="header__burger-line"></span>
                <span class="header__burger-line"></span>
              </label>
              <div class="header__burger-wrapper">
                <div class="header__menu-drawer" id="menu-drawer" :data-open="isShowMenu">
                  <button class="header__burger-close" @click="menuClose">
                    <img
                      src="/img/landing-tips/background/header/Close.svg"
                      alt="Закрыть меню"
                    />
                  </button>
                  <div class="header__menu-header">
                    <ul class="header__menu-list">
                      <li class="header__menu-item">
                        <a href="/login" target="_blank">
                          <b>Вход</b> / регистрация пользователя
                        </a>
                      </li>
                      <li class="header__menu-item">
                        <a href="/connect" target="_blank"> Подключить команду </a>
                      </li>
                      <li class="header__menu-item">
                        <a href="/connect" target="_blank" @click="modalOpen">
                          Подключить мастера индивидуально
                        </a>
                      </li>
                    </ul>
                  </div>
                  <div class="header__menu-body">
                    <ul class="header__menu-list header__menu-list--colored">
                      <li class="header__menu-item">
                        <a href="#process"> Как пользоваться </a>
                      </li>
                      <!-- <li class="header__menu-item">
                        <a href="#"> Как подключиться </a>
                      </li> -->
                      <li class="header__menu-item">
                        <a href="#payment"> Стоимость подключения </a>
                      </li>
                      <li class="header__menu-item">
                        <a href="#about"> О нас </a>
                      </li>
                      <li class="header__menu-item">
                        <a href="#question"> Частые вопросы </a>
                      </li>
                      <li class="header__menu-item">
                        <a href="#contacts"> Контакты </a>
                      </li>
                    </ul>
                  </div>
                  <div class="header__menu-footer">
                    <a
                      href="mailto:mastera@yagoda.team"
                      target="_blank"
                      class="header__menu-link"
                    >
                      mastera@yagoda.team
                    </a>
                    <div class="header__menu-social">
                      <a href="https://t.me/+79153690251"><img
                        src="/img/landing-tips/content/header/burger/tg.svg"
                        alt="Иконка Telegram"
                        class="header__menu-social-img"
                      /></a>
                      <a href="https://wa.me/79153690251"><img
                        src="/img/landing-tips/content/header/burger/whatsApp.svg"
                        alt="Иконка WhatsApp"
                        class="header__menu-social-img"
                      /></a>
                    </div>
                    <a class="header__menu-button" href="/connect">Подключиться</a>
                  </div>
                </div>
              </div>
            </div>
            <div class="header__logo">
              <a href="/" class="header__link">
                <img
                  src="/img/landing-tips/content/header/logo.svg"
                  alt="Логотип Ягоды"
                  class="header__logo-img"
                />
              </a>
            </div>
          </div>
          <div class="header__nav-right">
            <a type="button" class="header__btn" href="/connect">
              подключиться
              <span class="span header__first"></span>
              <span class="span header__second"></span>
              <span class="span header__third"></span>
              <span class="span header__fourth"></span>
            </a>
            <div class="header__enter">
              <a href="/login" target="_blank">
                <p class="header__text">Войти | Регистрация</p>
              </a>
              <a href="/login">
                <img
                  src="/img/landing-tips/content/header/enter.svg"
                  alt="Картинка входа в личный кабинет"
                  class="header__img"
                />
              </a>

            </div>
          </div>
        </nav>
      </div>
    </header>

    <main>
      <section class="promo">
        <div class="container promo__container">
          <div class="promo__content">
            <div class="promo__header">
              <h1 class="promo__heading">Удобный сервис приема чаевых</h1>
              <h2 class="promo__subheading">
                <span class="promo__subheading-span"> в составе </span>
                команды
                <span class="promo__subheading-span"> или </span>
                индивидуально.
              </h2>
            </div>
            <div class="promo__body">
              <ul class="promo__list">
                <li class="promo__list-item">
                  <div class="promo__item-box">
                    <img
                      class="promo__item-img"
                      src="/img/landing-tips/content/promo/1.svg"
                      alt="Картинка декор"
                    />
                  </div>
                  <p class="promo__item-text">
                    <b> Моментальное зачисление </b> чаевых сразу на карту.
                    <span class="span__green">
											Деньги не зависают в системе.
										</span>
                  </p>
                </li>
                <li class="promo__list-item">
                  <div class="promo__item-box">
                    <img
                      class="promo__item-img"
                      src="/img/landing-tips/content/promo/2.svg"
                      alt="Картинка декор"
                    />
                  </div>
                  <p class="promo__item-text">
                    <b> Никаких личных кабинетов </b> для вывода денег.
                    <span class="span__green">
											Не нужно вести личный кабинет, настраивать выплаты,
											платить комиссии.
										</span>
                  </p>
                </li>
                <li class="promo__list-item">
                  <div class="promo__item-box">
                    <img
                      class="promo__item-img"
                      src="/img/landing-tips/content/promo/3.svg"
                      alt="Картинка декор"
                    />
                  </div>
                  <p class="promo__item-text">
                    <b> Распределение чаевых </b> между сотрудниками.
                    <span class="span__green">
											(администраторам, охранникам, клинерам...)
										</span>
                  </p>
                </li>
                <li class="promo__list-item">
                  <div class="promo__item-box">
                    <img
                      class="promo__item-img"
                      src="/img/landing-tips/content/promo/4.svg"
                      alt="Картинка декор"
                    />
                  </div>
                  <p class="promo__item-text">
                    <b> Самостоятельное подключение </b> к системе.
                    <span class="span__green">
											Быстро, без согласований. Чаевые начнете получать уже
											через 10 минут.
										</span>
                  </p>
                </li>
              </ul>
            </div>
          </div>
          <div class="promo__illustration">
            <div class="promo__visual"></div>
            <img
              class="promo__img"
              src="/img/landing-tips/common/promo/promo.png"
              alt="Картинка девушки-промо"
            />
          </div>
        </div>
      </section>

      <section class="advantages">
        <div class="container">
          <div class="advantages__grid">
            <div class="advantages__item">
              <p class="advantages__num advantages__num_first">372</p>
              <p class="advantages__description advantages__description_first">
                индивидуальных QR подключено
              </p>
            </div>
            <div class="advantages__item">
              <p class="advantages__num advantages__num_third">128</p>
              <p class="advantages__description advantages__description_third">
                команд получателей чаевых подключено
              </p>
            </div>
            <div class="advantages__item">
              <p class="advantages__num advantages__num_fifth">
                72<span class="advantages__span_perсent">%</span>
              </p>
              <p class="advantages__description advantages__description_fifth">
                Среднее увеличение чаевых используя стойку Yagoda Tips
              </p>
            </div>
            <div class="advantages__item">
              <p class="advantages__num advantages__num_second">
                12<span class="advantages__span">мин</span>
              </p>
              <p class="advantages__description advantages__description_second">
                Среднее время подключения к системе для команд
              </p>
            </div>
            <div class="advantages__item">
              <p class="advantages__num advantages__num_fourth">
                6 <span class="advantages__span">мин</span>
              </p>
              <p class="advantages__description advantages__description_fourth">
                Среднее время подключения к системе индивидуально
              </p>
            </div>
            <div class="advantages__item">
              <p class="advantages__num advantages__num_sixth">max</p>
              <p class="advantages__description advantages__description_sixth">
                Мотивация команды сделать хорошо клиенту
              </p>
            </div>
          </div>
        </div>
      </section>

      <section class="tabs">
        <div class="container">
          <div class="tabs__wrapper">
            <ul class="tabs__group">
              <div class="tabs__decor tabs__decor_first"></div>
              <li data-tabs="tab-1" class="tabs__title tabs__title_active">чаевые <b>в команде</b></li>
              <div class="tabs__decor tabs__decor_second"></div>
              <li data-tabs="tab-2" class="tabs__title" @click="modalOpen">чаевые <b>мастеру</b></li>
              <div class="tabs__decor tabs__decor_third"></div>
            </ul>
            <div class="tabs__area">
              <div class="tabs__content" id="tab-1">
                <div class="tabs__header">
                <p class="tabs__text tabs__text_spaced">
                  С Yagoda Tips можно
                  <b>часть чаевых направлять другим сотрудникам:</b>
                  администраторам, уборщикам, охранникам или на общие расходы.
                  Или оставлять все чаевые мастерам, кому они были направлены.
                </p>
              </div>
                <div class="tabs__body">
                  <div class="tabs__inner">
                    <p class="tabs__text tabs__text_gap">
                      <b>Куратор команды</b> сможет:
                    </p>
                    <ul class="tabs__list">
                      <li class="tabs__item">
                        управлять <b>правилами распределения</b> чаевых между
                        сотрудниками, или оставлять 100% чаевых тому мастеру,
                        кому они были направлены.
                      </li>
                      <li class="tabs__item">
                        <b>добавлять новых</b> сотрудников в команду
                      </li>
                      <li class="tabs__item">
                        <b>управлять сотрудниками в смене</b>
                      </li>
                      <li class="tabs__item">
                        создавать <b>QR коды на команду</b> мастеров, где клиент
                        сможет выбрать своего мастера и оплатить ему чаевые.
                      </li>
                      <li class="tabs__item">
                        создавать <b>индивидуальные QR коды</b> мастера, чаевые
                        от которых будут разделяться между сотрудниками в
                        соответствии с правилами команды (если их установили).
                      </li>
                    </ul>
                    <p class="tabs__text tabs__text_gap">
                      Мастера будут видеть объем своих чаевых и чаевые
                      полученные коллегами, что будет мотивировать работать
                      лучше, дабы стать лидером.
                    </p>
                  </div>
                  <div class="tabs__content-box">
                    <div class="tabs__content-inner">
                      <p class="tabs__content-text">
                        Команда получателей чаевых
                        <b>никак не связана с организацией,</b> в стенах которой
                        работает.
                      </p>
                      <p class="tabs__content-text">
                        Организация не несет никакой ответственности за
                        сотрудников, в рамках получения ими чаевых.
                      </p>
                      <p class="tabs__content-text">
                        <b>Важно: чаевые,</b> полученные напрямую от клиента,
                        <b>являются дарением и не подлежат налогообложению</b> в
                        соответствии с законодательством РФ.
                      </p>
                    </div>
                  </div>
                </div>
              </div>
              <div class="tabs__content tabs__content_hidden" id="tab-2">

              </div>
            </div>
          </div>
        </div>
      </section>

      <section class="process" id="process">
        <div class="container">
          <div class="process__wrapper">
            <div class="process__header">
              <h3 class="process__heading">Как это работает</h3>
            </div>
            <div class="process__body">
              <ul class="process__list">
                <li class="process__item">
                  <div class="process__img-box">
                    <img
                      src="/img/landing-tips/content/process/1.svg"
                      alt="Картинка декор"
                      class="process__img"
                    />
                  </div>
                  <p class="process__text">1. Клиент сканирует QR-код.</p>
                </li>
                <li class="process__item">
                  <div class="process__img-box">
                    <img
                      src="/img/landing-tips/content/process/2.svg"
                      alt="Картинка декор"
                      class="process__img"
                    />
                  </div>
                  <p class="process__text">2. Выбирает мастера</p>
                </li>
                <li class="process__item">
                  <div class="process__img-box">
                    <img
                      src="/img/landing-tips/content/process/3.svg"
                      alt="Картинка декор"
                      class="process__img"
                    />
                  </div>
                  <p class="process__text">
                    3. Выбирает сумму чаевых и оплачивает.
                  </p>
                </li>
                <li class="process__item">
                  <div class="process__img-box">
                    <img
                      src="/img/landing-tips/content/process/4.svg"
                      alt="Картинка декор"
                      class="process__img"
                    />
                  </div>
                  <p class="process__text">
                    4. Чаевые моментально попадают на карту мастера
                  </p>
                </li>
              </ul>
            </div>
            <div class="process__illustration">
              <div class="process__visual"></div>
              <img
                src="/img/landing-tips/common/process/phones.png"
                alt="Картинка телефонов"
                class="process__img-head"
              />
            </div>
          </div>
        </div>
      </section>

      <section class="gains">
        <div class="container">
          <div class="gains__wrapper">
            <div class="gains__header">
              <h3 class="gains__heading">Преимущества</h3>
            </div>
            <div class="gains__body">
              <div class="gains__card">
                <div class="gains__img-box">
                  <img
                    src="/img/landing-tips/content/gains/1.svg"
                    alt="Картинка декор"
                    class="gains__img"
                  />
                </div>
                <div class="gains__text-box">
                  <p class="gains__text">
                    <b>Прямое зачисление чаевых сразу на карту.</b>
                  </p>
                  <p class="gains__text">
                    При оплате клиентом чаевых по СБП? деньги поступают на
                    карту сотрудника сразу.
                  </p>
                  <p class="gains__text">
                    При оплате картой - на утро следующего дня
                  </p>
                </div>
              </div>

              <div class="gains__card">
                <div class="gains__img-box">
                  <img
                    src="/img/landing-tips/content/gains/2.svg"
                    alt="Картинка декор"
                    class="gains__img"
                  />
                </div>
                <div class="gains__text-box">
                  <p class="gains__text">
                    <b>Никаких личных кабинетов</b> для вывода денег.
                  </p>
                  <p class="gains__text">Зарегистрировался — и всё!</p>
                  <p class="gains__text">
                    Не нужно следить за балансом и платить комиссии за вывод
                    денег.
                  </p>
                  <p class="gains__text">
                    Деньги поступают прямо на карту сотрудника.
                  </p>
                </div>
              </div>

              <div class="gains__card">
                <div class="gains__img-box">
                  <img
                    src="/img/landing-tips/content/gains/3.svg"
                    alt="Картинка декор"
                    class="gains__img"
                  />
                </div>
                <div class="gains__text-box">
                  <p class="gains__text">
                    <b>Самостоятельное подключение к системе.</b>
                  </p>
                  <p class="gains__text">Без согласований.</p>
                  <p class="gains__text">
                    Среднее время подключения к системе — 6 мин для мастера и 12
                    минут для команд.
                  </p>
                </div>
              </div>
              <div class="gains__card">
                <div class="gains__img-box">
                  <img
                    src="/img/landing-tips/content/gains/4.svg"
                    alt="Картинка декор"
                    class="gains__img"
                  />
                </div>
                <div class="gains__text-box">
                  <p class="gains__text">
                    В нашей службе заботы <b>живые люди</b>: быстро и полно
                    ответят на любой ваш вопрос.
                  </p>
                  <p class="gains__text">Без чатботов, роботов и заявок.</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section class="whom">
        <div class="container">
          <div class="whom__wrapper">
            <div class="whom__header">
              <h3 class="whom__heading">Кому</h3>
            </div>
            <div class="whom__body">
              <div class="whom__card">
                <div class="whom__background">
                  <img
                    src="/img/landing-tips/background/whom/flora.png"
                    alt="Изображение девушки флориста"
                    class="whom__background--first"
                  />
                </div>
                <p class="whom__text">Флористы</p>
              </div>
              <div class="whom__card">
                <div class="whom__background">
                  <img
                    src="/img/landing-tips/background/whom/barista.png"
                    alt="Изображение мужчины бариста"
                    class="whom__background--second"
                  />
                </div>
                <p class="whom__text">Бариста</p>
              </div>
              <div class="whom__card">
                <div class="whom__background">
                  <img
                    src="/img/landing-tips/background/whom/beauty.png"
                    alt="Изображение девушки бьюти-мастера"
                    class="whom__background--third"
                  />
                </div>
                <p class="whom__text">Стилисты</p>
              </div>
              <div class="whom__card">
                <div class="whom__background">
                  <img
                    src="/img/landing-tips/background/whom/massage.png"
                    alt="Изображение мужчины массажиста"
                    class="whom__background--fourth"
                  />
                </div>
                <p class="whom__text">Массажисты</p>
              </div>
              <div class="whom__card">
                <div class="whom__background">
                  <img
                    src="/img/landing-tips/background/whom/vet.png"
                    alt="Изображение мужчины ветеринара"
                    class="whom__background--fifth"
                  />
                </div>
                <p class="whom__text">Ветеринары</p>
              </div>
              <div class="whom__card-alt">
                <div class="whom__background-alt">
                  <div class="whom__background--sixth">
                    <svg
                      width="31"
                      height="30"
                      fill="none"
                      xmlns="http://www.w3.org/2000/svg"
                    >
                      <path
                        d="m14.153 26.904-.002-.001c-4.015-3.841-7.247-6.94-9.49-9.834-2.228-2.874-3.36-5.4-3.36-8.077C1.3 4.594 4.531 1.3 8.524 1.3c2.278 0 4.506 1.126 5.967 2.921l1.008 1.24 1.009-1.24c1.46-1.795 3.689-2.92 5.966-2.92 3.993 0 7.225 3.293 7.225 7.691 0 2.677-1.133 5.203-3.36 8.077-2.243 2.895-5.475 5.993-9.49 9.834l-.003.002-1.347 1.293-1.347-1.294Z"
                        stroke="currentColor"
                        stroke-width="2.6"
                      />
                    </svg>
                  </div>
                </div>
                <p class="whom__text">Другое</p>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section class="use">
        <div class="container">
          <div class="use__wrapper">
            <div class="use__content">
              <div class="use__header">
                <h3 class="use__heading">Как использовать</h3>
              </div>
              <div class="use__body">
                <ul class="use__list">
                  <li class="use__item">
                    Один QR размещаете на рабочий стол, другой – в прикассовую
                    зону.
                  </li>
                  <li class="use__item">
                    <b>
                      Клиент в ожидании сборки букета обязательно увидит стойку
                      и её добрый намек.
                    </b>
                  </li>
                  <li class="use__item">
                    Предоставляемый от Ягода QR может быть нанесен на стойку или
                    стикер.
                  </li>
                </ul>
              </div>
              <div class="use__footer">
                <a type="button" class="use__btn" href="/connect">
                  Подключить команду
                  <span class="span use__first"></span>
                  <span class="span use__second"></span>
                  <span class="span use__third"></span>
                  <span class="span use__fourth"></span>
                </a>
              </div>
            </div>
            <div class="use__illustration">
              <div class="use__visual"></div>
              <img
                src="/img/landing-tips/common/use/decor.png"
                alt="Картинка корзины с цветами"
                class="use__img"
              />
            </div>
          </div>
        </div>
      </section>

      <section class="connection">
        <div class="container">
          <div class="connection__wrapper">
            <div class="connection__illustration">
              <img
                src="/img/landing-tips/content/connection/phone.png"
                alt="Картинка телефона"
                class="connection__img"
              />
            </div>
            <div class="connection__content">
              <div class="connection__header">
                <h3 class="connection__heading">Как подключить</h3>
              </div>
              <div class="connection__body">
                <p class="connection__text">Займет <b>не более 15 минут</b></p>
                <ol class="connection__list">
                  <li class="connection__item">
                    <b>Регистрируетесь</b> как пользователь
                  </li>
                  <li class="connection__item">
                    Заводите <b>краткие данные</b> команды
                  </li>
                  <li class="connection__item">
                    <b>Заводите мастеров</b> и других сотрудников. Им придет СМС
                    со ссылкой на регистрацию.
                  </li>
                  <li class="connection__item">
                    <b>Получаете QR коды </b>для команды и индивидуальные QR
                    коды для мастеров*
                  </li>
                  <li class="connection__item">
                    Начните <b>принимать чаевые.</b>
                  </li>
                </ol>
                <p class="connection__text">
                  * Для оперативности сразу после регистрации распечатайте QR
                  коды. Одновременно закажите доставку стоек Ягода с QR кодами,
                  они более привлекательны и заметны. Стойки и стикеры
                  предоставляются бесплатно.
                </p>
              </div>
              <div class="connection__footer">
                <a type="button" class="connection__btn" href="/connect">
                  Подключить команду
                  <span class="span connection__first"></span>
                  <span class="span connection__second"></span>
                  <span class="span connection__third"></span>
                  <span class="span connection__fourth"></span>
                </a>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section class="payment" id="payment">
        <div class="container">
          <div class="payment__wrapper">
            <div class="payment__header">
              <h3 class="payment__heading">
                Стоимость подключения и использования
              </h3>
            </div>
            <div class="payment__body">
              <div class="payment__box">
                <p class="payment__null">
                  0<span class="payment__null-rub">₽</span>
                </p>
              </div>
              <ul class="payment__list">
                <li class="payment__item">Подключение <b>0р</b></li>
                <li class="payment__item">Абонентская плата <b>0р</b></li>
                <li class="payment__item">
                  Комиссия за вывод денег - <b>отсутствует как явление</b>
                </li>
                <li class="payment__item">
                  Стойки и стикеры Ягода с QR - <b>0р</b>
                </li>
              </ul>
            </div>
            <div class="payment__footer">
              <p class="payment__text">
                Компенсация транзакционных расходов — <b>0р</b> в 97% случаев.
              </p>
              <p class="payment__text">
                (Компенсация составляет 7% и почти всегда оплачивается клиентом.
                Если клиент откажется её оплачивать, нам придется взять её из
                чаевых.)
              </p>
            </div>
          </div>
        </div>
      </section>

      <section class="about" id="about">
        <div class="container">
          <div class="about__wrapper">
            <div class="about__header">
              <h3 class="about__heading">О нас</h3>
            </div>
            <div class="about__body">
              <div class="about__content">
                <p class="about__text">
                  Привет, друзья. Меня зовут Язев Игорь.
                </p>
                <p class="about__text">
                  <b>Yagoda</b> задумана как продукт, закрывающий мои личные
                  неудобства.
                </p>
                <p class="about__text">
                  Неудобства благодарить чаевыми: не только Мастеров, но и милых
                  Администраторов, внимательных Уборщиков...
                </p>
                <p class="about__text">
                  Причем не только Мастеров в салонах красоты, но и Флористов за
                  фантастически красиво собранный букет, Ветеринаров, сердечно
                  починивших мою собачку, Автомойщиков за внимание к моей
                  ласточке. Везде, где соприкасаюсь с классными Мастерами и их
                  окружением.
                </p>
                <p class="about__text">
                  Приятно, что еще и для самих заведений удалось снизить
                  эквайринг на 1,5%.
                </p>
                <p class="about__text">
                  Уверен, это скажется на качестве сервиса, который мы будем
                  получать от счастливых мастеров.
                </p>
                <p class="about__text">Пошли с нами.</p>
              </div>
              <div class="about__contact-card">
                <img
                  src="/img/landing-tips/content/about/yazev.png"
                  alt="Изображение Игоря Язева"
                  class="about__contact-card-img"
                />
                <span class="about__contact-card-title">Игорь Язев</span>
                <div class="about__social-box">
                  <a href="https://t.me/+79153690251" target="_blank" class="about__social-link">
                    <img
                      src="/img/landing-tips/content/about/TG.svg"
                      alt="Иконка Telegram"
                      class="about__social-img"
                    />
                  </a>
<!--                  <a href="https://wa.me/79153690251" target="_blank" class="about__social-link">-->
<!--                    <img-->
<!--                      src="/img/landing-tips/content/about/insta.svg"-->
<!--                      alt="Иконка Instagram"-->
<!--                      class="about__social-img"-->
<!--                    />-->
<!--                  </a>-->
                </div>
              </div>
            </div>
            <p class="about__text">Я собрал афигенную команду...</p>
            <div class="about__footer">
              <div class="about__team">
                <div class="about__team-member">
                  <img
                    src="/img/landing-tips/content/about/sasha.png"
                    alt="Картинка члена команды"
                    class="about__team-img"
                  />
                  <span class="about__team-name">Саша</span>
                </div>
                <div class="about__team-member">
                  <img
                    src="/img/landing-tips/content/about/nastya.png"
                    alt="Картинка члена команды"
                    class="about__team-img"
                  />
                  <span class="about__team-name">Настя</span>
                </div>
                <div class="about__team-member">
                  <img
                    src="/img/landing-tips/content/about/eva.png"
                    alt="Картинка члена команды"
                    class="about__team-img"
                  />
                  <span class="about__team-name">Ева</span>
                </div>
                <div class="about__team-member">
                  <img
                    src="/img/landing-tips/content/about/dima.png"
                    alt="Картинка члена команды"
                    class="about__team-img"
                  />
                  <span class="about__team-name">Дима</span>
                </div>
                <div class="about__team-member">
                  <img
                    src="/img/landing-tips/content/about/alexei.png"
                    alt="Картинка члена команды"
                    class="about__team-img"
                  />
                  <span class="about__team-name">Алексей</span>
                </div>
                <div class="about__team-member">
                  <img
                    src="/img/landing-tips/content/about/sergei.png"
                    alt="Картинка члена команды"
                    class="about__team-img"
                  />
                  <span class="about__team-name">Сергей</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section class="accordion" id="question">
        <div class="container">
          <div class="accordion__wrapper">
            <div class="accordion__header">
              <h3 class="accordion__heading">Частые вопросы</h3>
            </div>
            <div class="accordion__body">
              <div class="accordion__content-box"
                   v-for="(faq, index) in faqs"
                   :key="index">
                <div class="accordion__content"
                     :class="{ active: faq.active }"
                     @click="toggleFaq(index)"
                >
                  <div class="accordion__question">
                    {{ faq.question }}
                  </div>
                  <div class="accordion__response">{{ faq.answer }}</div>
                </div>
              </div>
            </div>
            <div class="accordion__footer">
              <a type="button" class="accordion__btn" href="/connect">
                Подключить <b>команду</b>
                <span class="span accordion__first"></span>
                <span class="span accordion__second"></span>
                <span class="span accordion__third"></span>
                <span class="span accordion__fourth"></span>
              </a>
              <a type="button" class="accordion__btn" @click="modalOpen">
                Подключить <b>индивидуально</b>
                <span class="span accordion__first"></span>
                <span class="span accordion__second"></span>
                <span class="span accordion__third"></span>
                <span class="span accordion__fourth"></span>
              </a>
            </div>
          </div>
        </div>
      </section>
    </main>

    <footer class="footer" id="contacts" v-if="false">
      <div class="container">
        <div class="footer__wrapper">
          <div class="footer__callback">
            <div class="footer__call">
              <div class="footer__box-first">
                <a href="tel:+74992132233" target="_blank" class="footer__link">
                  <img
                    src="/img/landing-tips/content/footer/phone.svg"
                    alt="Изображение телефона"
                    class="footer__img"
                  />
                </a>
              </div>
              <div class="footer__box-second">
                <a href="tel:+74992132233" target="_blank">
                  <p class="footer__call-text">Перезвоните мне</p>
                </a>
              </div>
            </div>
            <form action="" class="footer__form">
              <input
                class="footer__input"
                type="tel"
                placeholder="ваш телефон"
                id="phone"
              />
              <button class="footer__btn" type="submit">Отправить</button>
            </form>
          </div>
          <div class="footer__content">
            <div class="footer__info">
              <p class="footer__info-text">ООО “Ягода”</p>
              <p class="footer__info-text">
                ИНН 0800024353, ОГРН 12408000010604,Юр. адрес: 358000,
                Республика Калмыкия,г. Элиста, ул. В.И. Ленина, дом 268, помещ.
                3, рабочее место 1
              </p>
              <p class="footer__info-text">
                <b>Контакты:</b>
                <a href="mailto:mastera@yagoda.team" target="_blank">
                  mastera@yagoda.team
                </a>
              </p>
            </div>
            <div class="footer__sos1al">
              <a href="https://t.me/+79153690251" target="_blank">
                <img
                  src="/img/landing-tips/content/footer/tg.svg"
                  alt="Картинка Telegram"
                  class="footer__sos1al-img"
                />
              </a>
              <a href="https://wa.me/79153690251" target="_blank">
                <img
                  src="/img/landing-tips/content/footer/whatsApp.svg"
                  alt="Картинка WhatsApp"
                  class="footer__sos1al-img"
                />
              </a>
              <a href="tel:+74992132233" target="_blank">
                +7 (499) 213-22-33
              </a>
            </div>
          </div>
        </div>
      </div>
    </footer>

    <!-- Footer -->
    <FrontFooterComponent/>

    <ModalPages :modalData="modalData" v-if="isShowModal" @close="modalClose"/>


  </div>
</template>
<script>

import {Pagination, Navigation} from 'swiper/modules';
import { Swiper, SwiperSlide } from 'swiper/vue';
import 'swiper/css/bundle';
import 'swiper/css/pagination';
import 'swiper/css/navigation';

import FrontFooterComponent from '@/Pages/Anonymous/Components/Front/FrontFooterComponent.vue'
import ModalPages from '@/Shared/Modals/ModalPages.vue'

export default {
  components: {
    ModalPages,
    FrontFooterComponent
  },
  data() {
    return {
      modalData: {
        title: 'Раздел в разработке',
        text: 'Уже скоро мы добавим его'
      },
      isShowModal: false,
      isShowMenu: false,
      faqs: [
        {
          question: "Как быстро я получу стойку Yagoda Tips с QR?",
          answer: "",
          active: true
        },
        {
          question: "Мне перевели чаевые. Как быстро я их получу?",
          answer: "",
          active: false
        },
        {
          question: "Могу ли я заказать брендированные стойки с QR?",
          answer: "",
          active: false
        },
        {
          question: "Какие карты поддерживаются?",
          answer: "",
          active: false
        },
        {
          question: "Нужно ли платить налоги с чаевых?",
          answer: "",
          active: false
        },
        {
          question: "Всё звучит слишком сладко. В чем ваша выгода?",
          answer: "",
          active: false
        },
        {
          question: "Как происходит вывод денег?",
          answer: "",
          active: false
        },
      ]
    }
  },
  methods: {
    modalClose() {
      this.isShowModal = false
    },
    modalOpen() {
      this.isShowModal = true
    },
    menuOpen() {
      this.isShowMenu = true;
    },
    menuClose() {
      this.isShowMenu = false
    },
    toggleFaq(index) {
      this.faqs[index].active = !this.faqs[index].active;
      this.$nextTick(() => {
        const answerEl = this.$refs['faqAnswer' + index];
        if (answerEl) {
          // Если ref возвращается как массив (в случае v-for)
          const el = Array.isArray(answerEl) ? answerEl[0] : answerEl;
          if (this.faqs[index].active) {
            el.style.height = el.scrollHeight + 16 + "px";
          } else {
            el.style.height = "0";
          }
        }
      });
    }
  }
}

</script>
<style lang="scss" scoped>

//region Normalize

html {
  line-height: 1.15;
  -webkit-text-size-adjust: 100%;
}

body {
  margin: 0;
}

main {
  display: block;
}

h1 {
  font-size: 2em;
  margin: 0.67em 0;
}

hr {
  box-sizing: content-box; /* 1 */
  height: 0; /* 1 */
  overflow: visible; /* 2 */
}

pre {
  font-family: monospace, monospace; /* 1 */
  font-size: 1em; /* 2 */
}

a {
  background-color: transparent;
}

abbr[title] {
  border-bottom: none; /* 1 */
  text-decoration: underline; /* 2 */
  text-decoration: underline dotted; /* 2 */
}

b,
strong {
  font-weight: bolder;
}

code,
kbd,
samp {
  font-family: monospace, monospace; /* 1 */
  font-size: 1em; /* 2 */
}

small {
  font-size: 80%;
}

sub,
sup {
  font-size: 75%;
  line-height: 0;
  position: relative;
  vertical-align: baseline;
}

sub {
  bottom: -0.25em;
}

sup {
  top: -0.5em;
}

img {
  border-style: none;
}

button,
input,
optgroup,
select,
textarea {
  font-family: inherit;
  font-size: 100%;
  line-height: 1.15;
  margin: 0;
}

button,
input {
  overflow: visible;
}

button,
select {
  text-transform: none;
}

button,
[type="button"],
[type="reset"],
[type="submit"] {
  --webkit-appearance: button;
}

button::-moz-focus-inner,
[type="button"]::-moz-focus-inner,
[type="reset"]::-moz-focus-inner,
[type="submit"]::-moz-focus-inner {
  border-style: none;
  padding: 0;
}

button:-moz-focusring,
[type="button"]:-moz-focusring,
[type="reset"]:-moz-focusring,
[type="submit"]:-moz-focusring {
  outline: 1px dotted ButtonText;
}

fieldset {
  padding: 0.35em 0.75em 0.625em;
}

legend {
  box-sizing: border-box;
  color: inherit;
  display: table;
  max-width: 100%;
  padding: 0;
  white-space: normal;
}

progress {
  vertical-align: baseline;
}

textarea {
  overflow: auto;
}

[type="checkbox"],
[type="radio"] {
  box-sizing: border-box;
  padding: 0;
}

[type="number"]::-webkit-inner-spin-button,
[type="number"]::-webkit-outer-spin-button {
  height: auto;
}

[type="search"] {
  --webkit-appearance: textfield;
  outline-offset: -2px;
}

[type="search"]::-webkit-search-decoration {
  -webkit-appearance: none;
}

::-webkit-file-upload-button {
  -webkit-appearance: button; /* 1 */
  font: inherit; /* 2 */
}

details {
  display: block;
}

summary {
  display: list-item;
}

template {
  display: none;
}

[hidden] {
  display: none;
}

//endregion


:global(:root) {
  --font-family-base: 'Montserrat';
  --font-family-common: 'Bebas Neue';

  --color-lite: #ffffff;
  --color-alt-lite: #e3efef;
  --color-dark: #000000;
  --color-green: #38b0b0;
  --color-alt-green: #7f8f8f;
  --color-gray-blue: #9ca4b5;
  --color-dsrk-blue: #404854;
  --color-burger:#455A64;
  --color-alt-text: #999999;
  --color-main-btn: #1e2530;
  --color-bg-use: #e8f1f1;
  --color-calc-card: #e5f1f1;

  --border: 1px solid var(--color-gray-blue);

  --border-radius-main-button: 50px;
  --border-radius-tabs: 30px 30px 0 0;
  --border-radius-bg-profession: 40px;
  --border-radius-button: 40px;
  --border-radius-cards: 20px;
  --border-radius-photo: 50%;
  --border-radius-input: 32px;
  --border-radius-burger-btn: 46px;

  --container-width: 1280px;

  --transition-duration: 0.3s;
}


@font-face {
  font-family: 'Bebas Neue';
  src: url(/img/landing-tips/fonts/BebasNeue/bebasneue-regular.ttf);
  font-weight: 400;
  font-style: normal;
}

@font-face {
  font-family: 'Montserrat';
  src: url(/fonts/Montserrat/Montserrat-ExtraBold.ttf);
  font-weight: 800;
  font-style: normal;
  font-display: swap;
}

// тут поменял из вёрстки
@mixin fluid-text($min, $max, $mobile, $desktop) {
  & {
    font-size: #{$min}px;
    @media only screen and (min-width: #{$mobile}px) {
      font-size: calc(
        #{$min}px + #{($max - $min)} * (100vw - #{$mobile}px) / (#{$desktop} - #{$mobile})
      );
    }

    @media only screen and (min-width: #{$desktop}px) {
      font-size: #{$max}px;
    }
  }
}


@mixin reset-button{
  padding: 0;
  background-color: transparent;
  border: none;
}


$desktop-above: 1280px;
$desktop: 1279px;

$laptop-above: 1025px;
$laptop: 1024px;

$laptop-medium-above: 993px;
$laptop-medium: 992px;

$tablet-above: 768px;
$tablet: 767px;

$tablet-medium-above: 671px;
$tablet-medium: 670px;

$mobile-above: 576px;
$mobile: 575px;

$mobile-small-above: 320px;
$mobile-small: 374px;

@mixin desktop-above {
  @media (min-width: $desktop-above) {
    @content;
  }
}
@mixin desktop {
  @media (max-width: $desktop) {
    @content;
  }
}

@mixin laptop-above {
  @media (min-width: $laptop-above) {
    @content;
  }
}
@mixin laptop {
  @media (max-width: $laptop) {
    @content;
  }
}

@mixin laptop-medium-above {
  @media (min-width: $laptop-medium-above) {
    @content;
  }
}
@mixin laptop-medium {
  @media (max-width: $laptop-medium) {
    @content;
  }
}

@mixin tablet-above {
  @media (min-width: $tablet-above) {
    @content;
  }
}
@mixin tablet {
  @media (max-width: $tablet) {
    @content;
  }
}

@mixin tablet-medium-above {
  @media (min-width: $tablet-medium-above) {
    @content;
  }
}
@mixin tablet-medium {
  @media (max-width: $tablet-medium) {
    @content;
  }
}

@mixin mobile-above {
  @media (min-width: $mobile-above) {
    @content;
  }
}
@mixin mobile {
  @media (max-width: $mobile) {
    @content;
  }
}

@mixin mobile-small-above {
  @media (min-width: $mobile-small-above) {
    @content;
  }
}
@mixin mobile-small {
  @media (max-width: $mobile-small) {
    @content;
  }
}

@mixin hover {
  @media (hover: hover) {
    &:hover {
      @content;
    }
  }

  @media (hover: none) {
    &:active {
      @content;
    }
  }
}

.container{
  max-width: min(100%, var(--container-width));
  margin-inline: auto;
}

.hidden-mobile{
  @include mobile{
    display: none !important;
  }
}
.visible-mobile{
  @include mobile-above{
    display: none !important;
  }
}

html {
  scroll-behavior: smooth;
}

body {
  font-family: var(--font-family-base);
  font-weight: 400;
  color: var(--color-dark);
  background-color: var(--color-bg-first);
}

a,
button,
input,
textarea,
svg * {
  transition-duration: var(--transition-duration);
}

a {
  text-decoration: none;
  color: inherit;
}

h1 {
  @include fluid-text (54,70,576,1280);
}
h2 {
  @include fluid-text (23,40,576,1280);
}
h3 {
  font-size: 30px;
}


.base-background {
  border-radius: var(--border-radius-bg-profession);
  background-color: var(--color-green);
  height: 177px;
}

.btn {
  @include reset-button;
  @include fluid-text(14, 16, 576, 1280);

  position: relative;
  overflow: hidden;
  border-radius: var(--border-radius-button);
  cursor: pointer;
  z-index: 1000;
}

























.header {
  &__nav {
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: relative;

    @include mobile-small-above {
      padding-block: 6.16px;
      padding-left: 21px;
      padding-right: 16px;
      gap: 43px;
    }

    @include mobile-above {
      padding: 26px 29px 26px 32px;
      gap: 0;
    }

    &-left {
      display: flex;

      @include mobile-small-above {
        gap: 29px;
        align-items: center;
      }

      @include mobile-above {
        gap: 33px;
        align-items: flex-start;
      }
    }

    &-right {
      display: flex;
      align-items: center;

      @include mobile-above {
        gap: 40px;
      }

      @include desktop-above {
        gap: 20px;
      }
    }
  }

  &__burger {
    position: relative;
    z-index: 201;

    @include mobile-small-above {
      width: 28px;
      height: 24px;
    }

    @include mobile-above {
      width: 48px;
      height: 26px;
    }

    &-container {
      display: flex;
      flex-direction: column;
      gap: 13px;
      justify-content: center;
      position: relative;
      z-index: 202;
      cursor: pointer;

      @include mobile-small-above {
        width: 2rem;
        align-self: flex-start;
      }

      @include mobile-above {
        width: 3rem;
        height: 3rem;
        align-items: flex-end;
      }
    }

    &-input {
      display: none;
    }

    &-line {
      display: block;
      width: 100%;
      height: 3px;
      background: var(--color-burger);
      transition: all 300ms ease;
      transform-origin: left;
    }

    &-container:hover .header__burger-line:nth-of-type(1) {
      transform: scaleX(1);
    }

    &-container:hover .header__burger-line:nth-of-type(2) {
      transform: scaleX(0.5);
    }

    &-container:hover .header__burger-line:nth-of-type(3) {
      transform: scaleX(0.25);
    }
  }

  &__menu-drawer {
    position: fixed;
    top: 0;
    left: 0;
    padding: 81px 31px 105px;
    width: 100vw;
    height: 100vh;
    font-size: 16px;
    z-index: 200;
    background: var(--color-green);
    display: flex;
    flex-direction: column;
    gap: 93px;
    will-change: transform, opacity;
    transition: all 500ms cubic-bezier(0.14, 0.65, 0.15, 1);
    visibility: hidden;
    opacity: 0;
    transform: translateX(-100%);

    @include mobile-small-above {
      max-width: 275px;
    }

    @include mobile-above {
      max-width: 400px;
    }

    &[data-open='true'] {
      visibility: visible;
      opacity: 1;
      transform: translateX(0);
      pointer-events: auto;
      height: 100vh;
      max-height: 100vh;
    }
  }

  &__burger-wrapper {
    position: relative;
  }

  &__burger-close {
    @include reset-button;
    position: absolute;
    top: 0px;
    right: 0px;
    cursor: pointer;
  }

  &__menu-list {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-direction: column;
    gap: 24px;

    &--colored a {
      color: var(--color-lite) !important;
    }

    &-item {
      cursor: pointer;
    }
  }

  &__menu-footer {
    display: flex;
    flex-direction: column;
    gap: 24px;
  }

  &__menu-link {
    color: var(--color-lite);
  }

  &__menu-social {
    display: flex;
    gap: 17px;
  }

  &__menu-social-img {
    cursor: pointer;
  }

  &__menu-button {
    font-size: 14px;
    padding: 16px 29px 15px 30px;
    background-color: var(--color-dsrk-blue);
    color: var(--color-lite);
    border-radius: var(--border-radius-burger-btn);
    text-transform: uppercase;
    border: none;
    align-self: flex-start;
    cursor: pointer;
  }

  &__logo-img {
    @include mobile-small-above {
      max-width: 209px;
    }

    @include mobile-above {
      max-width: 244px;
    }
  }

  &__img {
    width: 18.75px;
    height: 18.75px;
  }

  &__btn {
    position: relative;
    overflow: hidden;
    font-size: 20px;
    padding: 24px 61px 24px 70px;
    color: var(--color-lite);
    background-color: var(--color-main-btn);
    border-radius: var(--border-radius-main-button);
    text-transform: uppercase;
    cursor: pointer;
    border: none;
    z-index: 1;
    display: inline-block;

    @include laptop-medium {
      padding: 24px 37px 24px 46px;
    }

    @include tablet {
      display: none;
    }

    span {
      position: absolute;
      width: 25%;
      height: 100%;
      background: var(--color-green);
      transition: all 0.5s ease-in-out;
      z-index: -1;
    }

    & .header__first {
      left: -25%;
      top: 0;
    }

    & .header__second {
      left: 25%;
      top: -100%;
    }

    & .header__third {
      left: 50%;
      top: 100%;
    }

    & .header__fourth {
      left: 100%;
      top: 0;
    }

    &:hover {
      .header__first {
        left: 0;
      }

      .header__second {
        top: 0;
      }

      .header__third {
        top: 0;
      }

      .header__fourth {
        left: 75%;
      }
    }
  }

  &__enter {
    display: flex;
    gap: 7px;
    align-items: center;
  }

  &__text {
    font-size: 14.87px;
    font-weight: 600;
    margin: 0;
    display: none;

    @include laptop-above {
      display: block;
    }
  }
}
.promo {
  &__container {
    display: flex;
    overflow: hidden;
    position: relative;

    @include mobile-small-above {
      flex-direction: column;
      gap: 30px;
    }

    @include mobile-above {
      flex-direction: row;
    }
  }

  &__content {
    display: flex;
    flex-direction: column;

    @include mobile-small-above {
      gap: 30px;
      padding: 23px 2px 310px 16px;
    }

    @include mobile-above {
      gap: 40px;
      padding: 20px 52px 34px 32px;
    }

    @include tablet-above {
      padding: 20px 0 66px 32px;
    }

    @include laptop-above {
      padding: 10.48px 0 60.52px 32px;
      gap: 73px;
    }
  }

  &__header {
    @include mobile-small-above {
      width: 100%;
    }

    @include mobile-above {
      width: 793px;
    }

    @include tablet-above {
      padding: 10px 0 10px 10px;
    }

    @include laptop-above {
      width: 906px;
    }
  }

  &__heading {
    margin: 0;
    text-transform: uppercase;
    font-weight: 700;

    @include mobile-small-above {
      line-height: 54px;
    }

    @include mobile-above {
      line-height: 79.24px;
    }

    @include tablet-above {
      line-height: 85.33px;
    }
  }
  &__subheading {
    margin-bottom: 0;
    margin-top: 30px;
    font-weight: 600;

    @include mobile-small-above {
      line-height: 29.26px;
      font-size: 24px;
    }

    @include mobile-above {
      line-height: 48.76px;
      font-size: 40px;
    }

    @include laptop-above {
      line-height: 0;
    }

    &-span {
      font-size: 40px;
      font-weight: 400;

      @include mobile-small-above {
        line-height: 29.26px;
        font-size: 24px;
      }

      @include mobile-above {
        line-height: 48.76px;
        font-size: 40px;
      }
    }
  }

  &__list {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 30px;
    margin: 0;
    padding: 0 !important;
    list-style: none;

    @include mobile-small-above {
      max-width: 100%;
    }

    @include mobile-above {
      max-width: 458px;
    }

    @include tablet-above {
      max-width: 596px;
    }

    @include laptop-above {
      max-width: 548px;
    }

    &-item {
      display: flex;

      @include mobile-small-above {
        gap: 11px;

        &:first-child,
        &:last-child {
          gap: 13px;
        }
      }

      @include mobile-above {
        gap: 19px;

        &:first-child,
        &:last-child {
          gap: 21px;
        }
      }
    }
  }

  &__item-box {
    @include mobile-small-above {
      align-self: start;
    }

    @include laptop-above {
      align-self: center;
    }
  }

  &__item-text {
    font-size: 20px;
    margin: 0;
  }

  &__illustration {
    @include mobile-small-above {
      position: absolute;
      bottom: -45px;
      right: 50%;
      translate: 40%;
      z-index: -100;
    }

    @include mobile-above {
      position: absolute;
      z-index: -100;
      bottom: 0;
      right: -194px;
      translate: 0;
    }

    @include tablet-above {
      top: 0;
      right: -194px;
    }
    @include laptop-above {
      top: 0;
      right: -242px;
    }
    @include desktop-above {
      top: 0;
      right: -56px;
    }
  }

  &__visual {
    &::before {
      content: '';
      background-image: url('/img/landing-tips/common/promo/promo.png');
      background-repeat: no-repeat;
      background-size: contain;
      background-position: center;
      margin: 0 auto;

      @include mobile-small-above {
        display: none;
      }

      @include mobile-above {
        display: block;
        width: 530px;
        height: 559px;
      }

      @include tablet-above {
        width: 549px;
        height: 867px;
      }
      @include laptop-above {
        width: 710px;
        height: 732px;
      }
    }
    &::after {
      content: '';
      background-image: url('/img/landing-tips/common/promo/qr.png');
      background-repeat: no-repeat;
      background-size: contain;
      background-position: center;
      margin: 0 auto;
      position: absolute;


      @include mobile-small-above {
        width: 130px;
        height: 178px;
        bottom: 27px;
        left: 212px;
      }

      @include mobile-above {
        width: 162px;
        height: 207px;
        bottom: -17px;
        left: 36px;
      }

      @include tablet-above {
        width: 162px;
        height: 207px;
        bottom: -17px;
        left: 36px;
      }
      @include laptop-above {
        width: 193px;
        height: 248px;
        bottom: -17px;
        left: 36px;
      }

      @include desktop-above {
        width: 193px;
        height: 249px;
        bottom: -17px;
        left: 0;
      }
    }
  }

  &__img {
    @include mobile-small-above {
      display: block;
      width: 260px;
      height: 323px;
    }

    @include mobile-above {
      display: none;
    }
  }
}
.span__green {
  color: var(--color-alt-green);
}
.advantages {
  background-color: var(--color-green);

  &__grid {
    color: var(--color-lite);
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;

    @include mobile-small-above {
      padding: 70px 29px 73px;
    }

    @include mobile-above {
      padding: 60px 34px;
    }

    @include tablet-above {
      padding: 60px 162px;
    }

    @include laptop-above {
      padding: 60px 180px 60px 60px;
    }
  }

  &__item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    min-width: 250px;
  }

  &__num {
    font-family: var(--font-family-common);
    font-weight: 700;
    font-size: 40px;
    line-height: 40px;
    margin: 0;
    white-space: nowrap;
    min-width: 60px;

    &_second,
    &_fifth {
      width: 63px !important;
    }

    &_first,
    &_third,
    &_fourth,
    &_sixth {
      text-align: right;
      width: 60px;
    }
  }

  &__span {
    line-height: 20px;
    font-size: 20px;
    font-weight: 700;
    font-family: var(--font-family-common);

    &_percent {
      line-height: 20px;
      font-size: 30px;
      font-weight: 700;
      font-family: var(--font-family-common);
    }
  }

  &__description {
    font-size: 16px;
    line-height: 16px;
    margin: 0;
    flex-grow: 1;
    min-width: 150px;

    &_first {
      width: 173px;
    }
    &_second,
    &_fourth {
      width: 251px;
    }
    &_third {
      width: 178px;
    }
    &_fifth {
      width: 247px;
    }
    &_sixth {
      width: 244px;
    }
  }
}
.tabs {
  &__wrapper {
    display: flex;
    flex-direction: column;
    gap: 42px;

    @include mobile-small-above {
      padding: 40px 0;
    }

    @include mobile-above {
      padding: 61px 32px 40px;
    }

    @include tablet-above {
      padding: 61px 31.5px 60px;
    }

    @include laptop-above {
      padding: 61px 135px 60px 134px;
    }
  }

  &__group {
    list-style: none;
    margin: 0;
    padding-left: 0;
    display: flex;
    justify-content: center;
  }

  &__title {
    border: var(--border);
    border-bottom: none;
    border-radius: var(--border-radius-tabs);

    @include mobile-small-above {
      padding: 15px 10px;
      font-size: 16px;
    }

    @include mobile-above {
      padding: 21.5px 52px;
      font-size: 14px;
    }

    @include tablet-above {
      padding: 21.5px 81.5px;
      font-size: 14px;
    }

    color: var(--color-lite);
    background-color: var(--color-dsrk-blue);
    display: block;

    &_active {
      background: none;
      color: #000;
    }
  }

  &__decor {
    border-bottom: var(--border);

    &_first,
    &_third {
      @include mobile-above {
        width: 12px;
      }

      @include tablet-above {
        width: 49px;
      }

      @include laptop-above {
        width: 75px;
      }

      @include desktop-above {
        min-width: 195px;
      }
    }

    &_second {
      @include mobile-small-above {
        width: 8px;
      }

      @include mobile-above {
        width: 20px;
      }
    }
  }

  &__body {
    @include mobile-small-above {
      display: flex;
      flex-direction: column;
      gap: 20px;
    }

    @include mobile-above {
      display: flex;
      justify-content: space-between;
      flex-direction: row;
    }

    @include tablet-above {
      gap: 12px;
      padding: 0;
    }

    @include laptop-above {
      gap: 22px;
    }
  }

  &__inner {
    display: flex;
    flex-direction: column;
    gap: 22px;

    @include mobile-small-above {
      padding: 10px 15px;
    }

    @include mobile-above {
      padding: 0;
      width: 401px;
    }

    @include tablet-above {
      width: 597px;
      padding-left: 10px;
    }
    @include laptop-above {
      padding-left: 10px;
      width: 597px;
    }
  }

  &__content-inner {
    display: flex;
    flex-direction: column;
    gap: 15px;
    background-color: var(--color-alt-lite);
    border: 1px solid var(--color-green);
    padding: 44px 42.5px;
    line-height: 19.2px;

    @include mobile-small-above {
      padding: 40px;
    }

    @include mobile-above {
      padding: 30px 22px;
      max-width: 277px;
    }

    @include tablet-above {
      max-width: 391px;
      padding: 44px 42.5px;
    }
  }

  &__content {
    display: flex;
    flex-direction: column;
    line-height: 19.2px;

    @include mobile-small-above {
      gap: 20px;
    }

    @include mobile-above {
      gap: 32px;
    }

    @include tablet-above {
      gap: 12px;
    }

    &_hidden {
      display: none;
    }
  }

  &__text {
    font-family: 16px;
    margin: 0;

    &_spaced {
      @include mobile-small-above {
        padding: 10px 14px 10px 17px;
      }

      @include mobile-above {
        padding: 10px 2px 10px;
      }

      @include tablet-above {
        padding: 10px 7.5px;
      }

      @include laptop-above {
        padding: 10px 6.5px;
      }
    }
  }

  &__list {
    margin: 0;
    padding-left: 25px;
    display: flex;
    flex-direction: column;
    gap: 10px;
    line-height: 19.2px;
  }

  &__content-box {
    @include mobile-small-above {
      padding: 0 15.5px;
    }

    @include mobile-above {
      padding: 0;
    }
  }

  &__content-text {
    font-size: 16px;
    margin: 0;
  }
}
.process {
  background-color: var(--color-alt-lite);
  &__wrapper {
    position: relative;
    z-index: 1;

    @include mobile-small-above {
      display: flex;
      flex-direction: column;
      padding: 30px 16px 143px;
    }
    @include mobile-above {
      display: flex;
      flex-direction: column;
      padding: 50px 0 58px 34px;
    }
  }

  &__header {
    margin-bottom: 50px;
  }

  &__heading {
    margin: 0;
    font-weight: 600;
  }

  &__body {
    display: flex;
    justify-content: space-between;
  }

  &__list {
    list-style: none;
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin: 0;
    padding-left: 77px;

    @include mobile-small-above {
      padding-left: 0;
    }

    @include laptop-above {
      padding-left: 77px;
    }
  }

  &__item {
    display: flex;
    gap: 16px;
    align-items: center;
  }

  &__img {
    display: flex;
    align-self: center;
  }

  &__text {
    @include fluid-text(14, 16, 576, 1280);

    margin: 0;
    color: var(--color-dsrk-blue);
  }

  &__illustration {
    position: absolute;

    @include tablet-above {
      top: 0px;
      right: 0px;
    }

    @include laptop-above {
      top: -86px;
      right: 0px;
    }

    @include desktop-above {
      top: -86px;
      right: 76px;
    }
  }

  &__visual {
    background-image: url('/img/landing-tips//common/process/phones.png');
    background-repeat: no-repeat;
    background-size: cover;
    position: relative;
    width: 695px;
    height: 400px;

    @include mobile-small-above {
      display: none;
    }

    @include tablet-above {
      display: block;
      width: 476px;
      height: 287px;
    }

    @include laptop-above {
      width: 635px;
      height: 400px;
    }
  }

  &__img-head {
    @include mobile-small-above {
      position: absolute;
      display: block;
      width: 257px;
      height: 184px;
      top: 277px;
      left: 45px;
      z-index: -1;
    }

    @include mobile-above {
      display: none;
    }
  }
}
.gains {
  background-color: var(--color-gray-blue);
  position: relative;
  z-index: 1;

  &__wrapper {
    padding: 50px 34px 53px 32px;
    display: flex;
    flex-direction: column;
    gap: 32px;


    @include tablet-above {
      padding: 50px 32px 43px 32px;
      position: relative;
    }

    @include laptop-above {
      padding: 50px 34px 53px 32px;
    }
  }

  &__heading {
    margin: 0;
    font-weight: 600;
  }

  &__body {
    display: flex;
    flex-wrap: wrap;
    gap: 29px;
    justify-content: center;
    align-items: stretch;

    @include mobile-small-above {
    }

    @include tablet-above {
      display: grid;
      grid-template-columns: repeat(2, minmax(250px, 1fr));
      gap: 29px;
    }

    @include laptop-above {
      grid-template-columns: repeat(2, 1fr);
    }

    @include desktop-above {
      grid-template-columns: repeat(4, 1fr);
    }
  }

  &__card {
    background-color: var(--color-lite);
    padding: 16px;
    border-radius: var(--border-radius-cards);
    display: flex;
    flex-direction: column;
    gap: 8px;
    width: 100%;
    height: 100%;
    box-sizing: border-box;
  }

  &__img {
    max-width: 100%;
    height: auto;
  }

  &__text-box {
    display: flex;
    flex-direction: column;
    gap: 12px;
    flex-grow: 1;
  }

  &__text {
    font-size: 16px;
    margin: 0;
    line-height: 19.2px;
  }
}
.whom {
  background: linear-gradient(rgba(64, 72, 84, 0.7), rgba(64, 72, 84, 0.7)),
  url('/img/landing-tips//content/whom/background.png');
  background-size: cover;
  background-position: center;

  &__wrapper {
    color: var(--color-lite);
    display: flex;
    flex-direction: column;
    gap: 67px;

    @include mobile-small-above {
      padding: 50px 19px 71px 27px;
    }

    @include mobile-above {
      padding: 50px 46px 66px 36px;
    }

    @include tablet-above {
      padding: 50px 18px 66px 36px;
    }

    @include laptop-above {
      padding: 50px 45px 61px 36px;
    }
  }

  &__heading {
    margin: 0;
    font-weight: 600;
  }

  &__body {
    display: grid;
    grid-template-columns: 1fr;
    row-gap: 44px;
    column-gap: 24px;
    grid-auto-rows: 1fr;

    @include mobile-small-above {
      grid-template-columns: repeat(2, minmax(150px, 1fr));
    }

    @media (min-width: 576px) and (max-width: 645px) {
      grid-template-columns: repeat(2, minmax(180px, 1fr));
    }

    @media (min-width: 646px) {
      grid-template-columns: repeat(3, minmax(180px, 1fr));
    }

    @include laptop-above {
      grid-template-columns: repeat(5, minmax(180px, 1fr));
      gap: 18px;
    }

    @include desktop-above {
      grid-template-columns: repeat(6, minmax(180px, 1fr));
      gap: 44px;
    }
  }

  &__card,
  &__card-alt {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    gap: 24px;
    position: relative;
    max-width: 180px;

    &:hover {
      .whom__text {
        font-weight: 700;
      }
    }
  }

  &__card-alt {
    width: 80px;
  }

  &__background,
  &__background-alt {
    width: 100%;
    height: 177px;
    display: flex;
    justify-content: center;
    align-items: center;
    background-repeat: no-repeat;
    background-color: var(--color-green);
    transition: background-color var(--transition-duration) ease-in-out;
    position: relative;
    max-width: 180px;
    border-radius: var(--border-radius-bg-profession);

    &:hover {
      background-color: var(--color-lite);
      border-radius: var(--border-radius-bg-profession);
    }
  }

  &__background-alt {
    width: 80px;

    &:hover {
      background-color: var(--color-lite);

      svg {
        color: var(--color-green);
        transform: scale(1.2);
        transition: var(--transition-duration);
      }
    }
  }

  &__background--first,
  &__background--second,
  &__background--third,
  &__background--fourth,
  &__background--fifth {
    @include mobile-small-above {
      width: 180px;
    }

    &:hover {
      transform: scale(1.1);
      transition: var(--transition-duration);
      position: absolute;
    }
  }

  &__background--first {
    position: absolute;

    @include mobile-small-above {
      top: -50px;
      left: -7px;
    }

    @include tablet-above {
      top: -49px;
      left: 0;
    }

    &:hover {
      @include mobile-small-above {
        top: -48px;
        left: -7px;
      }

      @include tablet-above {
        top: -59px;
        left: 0;
      }
    }
  }

  &__background--second {
    position: absolute;

    @include mobile-small-above {
      top: -58px;
      left: -11px;
      border-radius: 0 0 55px 55px;
    }

    @include tablet-above {
      top: -60px;
      left: 0;
    }

    &:hover {
      border-radius: 0 0 60px 60px;

      @include mobile-small-above {
        top: -69px;
        left: -11px;
      }

      @include tablet-above {
        top: -70px;
        left: 0;
      }
    }
  }

  &__background--third {
    position: absolute;

    @include mobile-small-above {
      top: -49px;
      left: -21px;
      border-radius: 0 0 50px 70px;
    }

    @include tablet-above {
      top: -51px;
      left: 0;
    }

    &:hover {
      border-radius: 0 0 50px 70px;

      @include mobile-small-above {
        top: -62px;
        left: -22px;
      }

      @include tablet-above {
        top: -61px;
        left: 0;
      }
    }
  }

  &__background--fourth {
    position: absolute;

    @include mobile-small-above {
      top: -50px;
      left: -9px;
    }

    @include tablet-above {
      top: -49px;
      left: 0;
    }

    &:hover {
      @include mobile-small-above {
        top: -59px;
        left: -16px;
      }

      @include tablet-above {
        top: -61px;
        left: 0;
      }
    }
  }

  &__background--fifth {
    border-radius: 0 0 65px 44px;
    position: absolute;

    @include mobile-small-above {
      top: -23px;
      left: -7px;
    }

    @include tablet-above {
      top: -27px;
      left: 0;
    }

    &:hover {
      border-radius: 0 0 69px 44px;

      @include mobile-small-above {
        top: -25px;
        left: 0;
      }

      @include tablet-above {
        top: -37px;
        left: 0;
      }
    }
  }

  &__background--sixth {
    position: absolute;
    top: 39px;
    left: 24px;
  }

  &__text {
    font-size: 25px;
    margin: 0;
    transition: var(--transition-duration);
  }
}
.use {
  background-color: var(--color-bg-use);
  &__wrapper {
    position: relative;
    overflow: hidden;

    @include mobile-small-above {
      padding: 40px 16px 52px;
      display: flex;
      flex-direction: column;
    }

    @include mobile-above {
      padding: 86px 0 80px 37px;
      gap: 24px;
    }

    @include tablet-above {
      padding: 80px 0 108px 37px;
      display: flex;
      flex-direction: row;
    }

    @include laptop-above {
      padding: 80px 0 108px 120px;
    }
  }

  &__content {
    display: flex;
    flex-direction: column;
    gap: 32px;
    z-index: 2;
  }

  &__heading {
    margin: 0;
    font-weight: 600;
  }

  &__list {
    font-size: 16px;
    margin: 0;
    padding-left: 0;
    list-style: none;
    display: flex;
    flex-direction: column;
    gap: 12px;
    max-width: 488px;
    line-height: 22.4px;

    @include mobile-above {
      width: 100%;
    }

    @include tablet-above {
      width: 453px;
    }

    @include laptop-above {
      width: 488px;
    }
  }

  &__btn {
    @extend .btn;

    background-color: var(--color-dsrk-blue);
    color: var(--color-lite);
    padding: 22px 60px;

  }

  span {
    position: absolute;
    width: 26%;
    height: 100%;
    background: var(--color-green);
    transition: all 0.7s;
    z-index: -100;
  }

  &__first {
    right: 100%;
    top: 0;
  }

  &__btn:hover &__first {
    right: 0;
  }

  &__second {
    left: 25%;
    top: -100%;
  }

  &__btn:hover &__second {
    top: 0;
    left: 50%;
  }

  &__third {
    left: 50%;
    top: 100%;
  }

  &__btn:hover &__third {
    top: 0;
    left: 25%;
  }

  &__fourth {
    left: 100%;
    top: 0;
  }

  &__btn:hover &__fourth {
    left: 0;
  }

  &__illustration {
    display: flex;
    justify-content: center;
    z-index: 1;
  }

  &__visual {
    background-image: url('/img/landing-tips/common/use/decor.png');
    background-position: 20px -110px;
    background-repeat: no-repeat;
    background-size: cover;
    position: absolute;

    @include mobile-small-above {
      display: none;
    }

    @include tablet-above {
      display: block;
      width: 558px;
      height: 340px;
      top: 44px;
      right: -63px;
    }

    @include laptop-medium-above {
      width: 558px;
      height: 340px;
      top: 44px;
      right: -53px;
    }

    @include laptop-above{
      width: 517px;
      height: 512px;
      top: 44px;
      right: 0;
    }
    @include desktop-above {
      width: 700px;
      height: 460px;
      top: 7px;
      right: 0px;
    }
  }

  &__img {
    @include mobile-small-above {
      width: 306px;
      height: 245px;
      display: block;
    }

    @include tablet-above {
      display: none;
    }
  }
}
.connection {
  background-color: var(--color-dsrk-blue);

  &__wrapper {
    color: var(--color-lite);

    @include mobile-small-above {
      padding: 50px 16px 294px;
      gap: 26px;
      display: flex;
      flex-direction: column-reverse;
      position: relative;
    }

    @include mobile-above {
      display: flex;
      flex-direction: row;
      padding: 80px 15px 60px 29px;
      gap: 26px;
    }

    @include tablet-above {
      padding: 80px 43px 67px 29px;
    }

    @include laptop-above {
      padding: 80px 14px 67px 116px;
      gap: 32px;
    }
  }

  &__illustration {
    display: flex;
    justify-content: center;
    align-items: center;

    @include mobile-small-above {
      position: absolute;
      bottom: -100px;
      right: 52px;

      img {
        max-width: 284px;
      }
    }

    @include mobile-above {
      position: static;
    }
  }

  &__img {
    max-width: 327px;
  }

  &__heading {
    margin: 0;
    font-weight: 600;
    margin-bottom: 32px;
  }

  &__body {
    display: flex;
    flex-direction: column;
    gap: 24px;
    line-height: 22.4px;
    margin-bottom: 56px;

    @include mobile-small-above {
      max-width: 343px;
    }

    @include mobile-above {
      max-width: 357px;
    }

    @include tablet-above {
      max-width: 599px;
    }
  }

  &__list {
    list-style: none;
    counter-reset: custom-counter;
    margin: 0;
    padding-left: 0;
    display: flex;
    flex-direction: column;
    gap: 12px;
  }

  &__item {
    counter-increment: custom-counter;
    position: relative;
    padding-left: 20px;

    &::before {
      content: counter(custom-counter) '.';
      font-weight: bold;
      position: absolute;
      left: 0;
    }
  }

  &__text {
    @include fluid-text(14, 16, 576, 1280);
    margin: 0;
  }

  &__btn {
    @extend .btn;

    background-color: var(--color-green);
    color: var(--color-lite);
    padding: 22px 60px;

    &:hover {
      color: var(--color-dsrk-blue);
    }
  }
  .span {
    position: absolute;
    width: 26%;
    height: 100%;
    background: var(--color-alt-lite);
    color: var(--color-dsrk-blue);
    transition: all 0.7s;
    z-index: -100;
  }

  &__first {
    right: 100%;
    top: 0;
  }

  &__btn:hover &__first {
    right: 0;
  }

  &__second {
    left: 25%;
    top: -100%;
  }

  &__btn:hover &__second {
    top: 0;
    left: 50%;
  }

  &__third {
    left: 50%;
    top: 100%;
  }

  &__btn:hover &__third {
    top: 0;
    left: 25%;
  }

  &__fourth {
    left: 100%;
    top: 0;
  }

  &__btn:hover &__fourth {
    left: 0;
  }
}
.payment {
  background-color: var(--color-green);
  &__wrapper {
    color: var(--color-lite);
    display: flex;
    flex-direction: column;
    gap: 32px;

    @include mobile-small-above {
      padding: 103px 16px 54px;
    }

    @include mobile-above {
      padding: 50px 40px 68px;
    }

    @include laptop-above {
      padding: 50px 240px 68px;
    }
  }

  &__heading {
    margin: 0;
    font-weight: 600;
  }

  &__body {
    display: flex;
    align-items: center;
    gap: 18px;
  }

  &__null {
    font-family: var(--font-family-common);
    font-size: 163px;
    font-weight: 700;
    margin: 0;

    &-rub {
      font-family: var(--font-family-common);
      font-size: 80px;
    }
  }

  &__list {
    margin: 0;
    padding-left: 0;
    list-style: none;
    font-size: 16px;
    display: flex;
    flex-direction: column;
    gap: 13px;
  }

  &__footer {
    display: flex;
    flex-direction: column;
    gap: 18px;
  }

  &__text {
    font-size: 16px;
    margin: 0;
    line-height: 22.4px;
  }
}
.about {
  &__wrapper {
    padding: 50px 255px 58px 240px;
    display: flex;
    flex-direction: column;
    gap: 40px;

    @include mobile-small-above {
      padding: 50px 16px 40px;
    }

    @include mobile-above {
      padding: 50px 32px 80px 32px;
    }

    @include tablet-above {
      padding: 50px 108px 120px 120px;
    }

    @include laptop-above {
      padding: 50px 245px 48px 240px;
    }
  }

  &__heading {
    margin: 0;
    font-weight: 600;
  }

  &__body {
    @include mobile-small-above {
      display: flex;
      flex-direction: column;
      gap: 16px;
    }

    @include mobile-above {
      display: flex;
      flex-direction: row;
      justify-content: space-between;
      gap: 16px;
    }
  }

  &__content {
    display: flex;
    flex-direction: column;
    gap: 16px;
    max-width: 626px;

    @include mobile-small-above {
      max-width: 100%;
    }

    @include mobile-above {
      max-width: 527px;
    }

    @include tablet-above {
      max-width: 626px;
    }
  }
  &__text {
    margin: 0;
    font-size: 16px;
  }

  &__contact-card {
    display: flex;
    flex-direction: column;
    gap: 10px;

    @include mobile-small-above {
      align-items: center;
      max-width: 143px;
    }
  }

  &__social-box {
    display: flex;
    gap: 9px;
  }

  &__team {
    display: flex;

    @include mobile-small-above {
      flex-wrap: wrap;
    }

    @include mobile-above {
      flex-wrap: wrap;
      gap: 0;
    }

    @include tablet-above {
      gap: 12px;
    }

    @include laptop-above {
      gap: 15.5px;
    }


    &-member {
      display: flex;
      flex-direction: column;
      gap: 10px;
    }

    &-name {
      align-self: center;
    }
  }
}
.accordion {
  &__wrapper {
    @include mobile-small-above {
      padding: 21px 16px 13px;
    }

    @include mobile-above {
      padding: 50px 47px 45px 32px;
    }

    @include tablet-above {
      padding: 50px 102px 53px 120px;
    }

    @include laptop-above {
      padding: 50px 239px 53px 240px;
    }
  }

  &__heading {
    margin: 0;
    font-weight: 600;
    margin-bottom: 32px;

    @include mobile-small-above {
      margin-bottom: 40px;
    }

    @include mobile-above {
      margin-bottom: 32px;
    }
  }

  &__body {
    margin-bottom: 60px;

    @include mobile-small-above {
      margin-bottom: 42px;
    }

    @include mobile-above {
      margin-bottom: 60px;
    }
  }

  &__content-box {
    border-bottom: var(--border);
    line-height: 22.4px;
  }
  &__content {
    display: flex;
    flex-direction: column;
    gap: 16px;
    position: relative;
    transition: gap var(--transition-duration) ease;
    cursor: pointer;

    @include mobile-small-above {
      padding-top: 10px;
      padding-bottom: 10px;
    }

    @include mobile-above {
      padding-top: 20px;
      padding-bottom: 27px;
    }
  }

  &__question {
    position: relative;
    padding-right: 30px;
    cursor: pointer;

    &::after {
      content: url('/img/landing-tips/background/accordion/plus.svg');
      position: absolute;
      top: 50%;
      right: 0;
      transform: translateY(-50%);
      width: 18px;
      height: 30px;
      transition: transform 0.3s;
    }
  }

  &__content.active &__question::after {
    content: url('/img/landing-tips/background/accordion/minus.svg');
    max-height: 1000px;
    transition: max-height var(--transition-duration) ease-in-out;
  }

  &__response {
    max-height: 0;
    overflow: hidden;
    transition: max-height var(--transition-duration) ease;
  }

  &__content:not(.active) {
    gap: 0;
  }

  &__text {
    font-size: 16px;
    margin: 0;
    cursor: pointer;
  }

  &__footer {
    @include mobile-small-above {
      display: flex;
      flex-direction: column;
      gap: 17px;
    }

    @include mobile-above {
      display: flex;
      flex-direction: row;
      justify-content: center;
      align-items: center;
      gap: 32px;
    }
  }

  &__btn {
    @extend .btn;

    background-color: var(--color-green);
    color: var(--color-lite);
    padding: 22px 58px;

    @include mobile-small-above {
      //padding: 22px 37px;
      padding: 22px 0;
      min-width: 300px;
      text-align: center;
    }

    @include mobile-above {
      //padding: 22px 35px;
      padding: 22px 0;
      min-width: 300px;
      text-align: center;
    }

    @include tablet-above {
      //padding: 22px 58px;
      padding: 22px 0;
      min-width: 363px;
      text-align: center;
    }


  }

  &__btn:first-child {
    @include mobile-small-above {
      //padding: 22px 79px;
      padding: 22px 0;
      min-width: 300px;
      text-align: center;
    }

    @include mobile-above {
      //padding: 22px 78px;
      padding: 22px 0;
      min-width: 300px;
      text-align: center;
    }

    @include tablet-above {
      padding: 22px 0;
      min-width: 363px;
      text-align: center;
    }
  }

  span {
    position: absolute;
    width: 26%;
    height: 100%;
    background: var(--color-dsrk-blue);
    transition: all 0.7s;
    z-index: -100;
  }

  &__first {
    right: 100%;
    top: 0;
  }

  &__btn:hover &__first {
    right: 0;
  }

  &__second {
    left: 25%;
    top: -100%;
  }

  &__btn:hover &__second {
    top: 0;
    left: 50%;
  }

  &__third {
    left: 50%;
    top: 100%;
  }

  &__btn:hover &__third {
    top: 0;
    left: 25%;
  }

  &__fourth {
    left: 100%;
    top: 0;
  }

  &__btn:hover &__fourth {
    left: 0;
  }
}
.footer {
  background-color: var(--color-dsrk-blue);
  &__wrapper {
    @include mobile-small-above {
      padding: 41px 16px 31px 16px;
    }

    @include mobile-above {
      display: flex;
      justify-content: space-between;
      gap: 31px;
      padding: 43px 54px 43px 40px;
    }

    @include tablet-above {
      padding: 43px 77px 43px 40px;
    }

    @include laptop-above {
      padding: 43px 129px 43px 245px;
    }
  }

  &__callback {
    display: flex;
    flex-direction: column;
    gap: 16px;

    @include mobile-small-above {
      width: 100%;
    }

    @include mobile-above {
      width: 324px;
    }

    @include tablet-above {
      width: 487px;
    }
  }

  &__call {
    display: flex;
    gap: 22px;

    &-text {
      font-size: 20px;
      margin: 0;
      color: var(--color-lite);
    }
  }

  &__box-second {
    display: flex;
    align-items: center;
  }

  &__form {
    position: relative;
  }

  &__input {
    flex-shrink: 0;
    border-radius: var(--border-radius-input);
    padding: 24px 32px;
    background-color: var(--color-lite);
    border: none;

    @include mobile-small-above {
      width: 279px;
    }

    @include mobile-above {
      width: 260px;
    }

    @include tablet-above {
      width: 423px;
    }
  }

  &__input::placeholder {
    color: var(--color-alt-text);
  }
  &__btn {
    @include reset-button;

    font-size: 20px;
    text-transform: uppercase;
    position: absolute;
    top: 23px;
    right: 37px;

    @include mobile-small-above {
      top: 20px;
      right: 57px;
    }

    @include mobile-above {
      top: 20px;
      right: 27px;
    }
  }

  &__content {
    display: flex;
    flex-direction: column;
    gap: 16px;
    line-height: 14.63px;

    @include mobile-small-above {
      max-width: 100%;
    }

    @include mobile-above {
      max-width: 332px;
    }

    @include tablet-above {
      max-width: 389px;
    }
  }

  &__info {
    display: flex;
    flex-direction: column;
    gap: 12px;
    color: var(--color-lite);

    @include mobile-small-above {
      margin-top: 31px;
      margin-bottom: 4px;
    }

    @include mobile-above {
      margin-top: 0px;
      margin-bottom: 0px;
    }

    &-text {
      font-size: 12px;
      margin: 0;
      line-height: 14.63px;
    }
  }

  &__sos1al {
    display: flex;
    gap: 19px;
    align-items: center;
    color: var(--color-lite);

    &-text {
      font-size: 16px;
    }
  }
}


//region Не в вёрстке
img {
  max-width: none;
}
.landing-wrapper {
  font-family: var(--font-family-base);
  font-weight: 400;
  color: var(--color-dark);
  //background-color: var(--color-bg-first);
  background-color: #fff;
}
.landing-wrapper>main {
  z-index: 2;
  position: relative;
}
.container {
  padding: 0;
}

.about__team {
  @media screen and (max-width: 767px) {
    gap: 12px;
  }
}
//endregion

</style>
