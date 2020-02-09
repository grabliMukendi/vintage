
function flipFilter() {
    let cards = Array.from(document.querySelectorAll('.js_cards'));
    cards.style.opacity = 0;
    cards.forEach((card) => {
        card.addEventListener('click', () => {
            alert('Bolok est mortel !');
            let flip = new Flip();
            flip.read(cards);
            card.parentNode.removeChild(card);
            flip.play(cards);
        });
    });

    class Flip {
        constructor() {
            this.duration = 500;
            this.position = {}
        }

        /**
         * Mémorise la position de nos éléments
         * @param {HTLMElement[]} elements
         */
        read(elements) {
            elements.forEach(elements => {
                const id = element.getAttribute('id');
                this.positions[id] = element.getBoundingClientRect();
            });
        }

        /**
         * Anime éléments vers leurs nouvelles positions
         * @param {HTLMElement[]} elements
         */
        play(elements) {
            elements.forEach(elements => {
                const id = element.getAttribute('id');
                const newPosition = element.getBoundingClientRect();
                const oldPosition = this.positions[id];
                const deltaX = oldPosition.x - newPosition.x;
                const deltaY = oldPosition.y - newPosition.y;
                const deltaWidth = oldPosition.width / newPosition.width;
                const deltaHeight = oldPosition.height / newPosition.height;

                element.animate([{
                    transform: `translate(${deltaX}px, ${deltaY}px) scale(${deltaWidth}, ${deltaHeight})`
                },
                {
                    transform: 'none'
                }],
                    {
                        duration: this.duration,
                        easing: 'ease-in-out',
                        fill: 'both'
                    });

                element.style.transform = `translate(${deltaX}px, ${deltaY}px) scale(${deltaWidth}, ${deltaHeight})`;

            });
        }

    }
}
flipFilter();