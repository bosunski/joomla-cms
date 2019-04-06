let currentArticle = Joomla.getOptions('seo').article;

String.prototype.visualLength = function()
{
	let ruler = document.getElementById("titleRuler");
	ruler.innerHtml = this;
	return ruler.offsetWidth;
};

document.addEventListener("DOMContentLoaded",
	(e) => new Vue({
		el: "#seo",
		data() {
			return {
				article: currentArticle || {},
				titlePixelLength: 0,
				descriptionPixelLength: 0,
			}
		},
		computed: {
			articleTitle: {
				get() {
					if (this.article.ogpg.seo_title) {
						return this.article.ogpg.seo_title;
					}

					return this.article.title;
				},
				set(newValue) {
					this.article.ogpg.seo_title = newValue;
				}
			},
			articleDescription: {
				get() {
					if (this.article.ogpg.seo_description && this.article.ogpg.seo_description !== "") {
						return this.article.ogpg.seo_description;
					}

					return this.article.metadesc;
				},
				set(newValue) {
					this.article.ogpg.seo_description = newValue;
				}
			},
			titleBarActiveClass() {
					if (this.titlePixelLength >= 500 && this.titlePixelLength < 607) return 'progress-bar-yellow';
					if (this.titlePixelLength >= 500 && this.titlePixelLength >= 607) return 'progress-bar-red';
					return 'progress-bar-blue';
			},
			descriptionBarActiveClass() {
				if (this.descriptionPixelLength >= 500 && this.descriptionPixelLength < 607) return 'progress-bar-yellow';
				if (this.descriptionPixelLength >= 500 && this.descriptionPixelLength >= 607) return 'progress-bar-red';
				return 'progress-bar-blue';
			}
		},
		watch: {
			articleTitle(newValue) {
				let ruler = this.$refs.titleRuler;
				ruler.innerText = newValue;
				this.titlePixelLength = ruler.offsetWidth;
			},
			articleDescription(newValue) {
				let ruler = this.$refs.descriptionRuler;
				ruler.innerText = newValue;
				this.descriptionPixelLength = ruler.offsetWidth;
			}
		}
	})
);
